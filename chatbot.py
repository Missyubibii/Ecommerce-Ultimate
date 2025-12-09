from flask import Flask, request, jsonify
import google.generativeai as genai
from google.api_core import exceptions
import os
import json
import time
from dotenv import load_dotenv

load_dotenv()
app = Flask(__name__)

# --- CẤU HÌNH API KEYS ---
API_KEYS_STR = os.getenv("GEMINI_API_KEYS") or os.getenv("GEMINI_API_KEY") or ""
API_KEYS = [k.strip() for k in API_KEYS_STR.split(',') if k.strip()]
current_key_index = 0

# --- CẤU HÌNH MODEL ---
# Dùng flash để phản hồi nhanh, temperature thấp để bám sát dữ liệu
MODEL_NAME = "gemini-flash-latest" 
GENERATION_CONFIG = {
    "temperature": 0.3, # Tăng nhẹ để văn phong tự nhiên hơn, nhưng vẫn kiểm soát tốt
    "top_p": 0.95,
    "top_k": 40,
    "max_output_tokens": 8192,
    "response_mime_type": "application/json",
}

# --- SYSTEM PROMPT CHUYÊN NGHIỆP ---
SYSTEM_PROMPT_TEMPLATE = """
Bạn là "Ultimate Assistant" - Chuyên viên tư vấn cấp cao của hệ thống Laravel E-Commerce Ultimate.
Bạn chuyên về các thiết bị công nghệ (Điện thoại, Laptop, PC, Phụ kiện).

DỮ LIỆU SẢN PHẨM HIỆN CÓ (CONTEXT):
{products_context}

LỊCH SỬ TRÒ CHUYỆN:
{chat_history}

NHIỆM VỤ CỦA BẠN:
1.  **Tư vấn sản phẩm:**
    - Cung cấp đầy đủ: Tên, Giá, Tồn kho.
    - So sánh các sản phẩm nếu khách phân vân (dựa trên thông số trong Context).
    - Nếu Tồn kho > 0: Mời gọi mua hàng (Call to Action).
    - Nếu Tồn kho = 0: Thông báo hết hàng và gợi ý mẫu tương đương.
    - Tuyệt đối TRUNG THỰC: Chỉ tư vấn sản phẩm có trong Context. Nếu Context rỗng và khách đang hỏi sản phẩm -> Báo chưa kinh doanh.

2.  **Quy trình Chốt đơn (QUAN TRỌNG):**
    - Khi khách quyết định mua (nói "chốt", "mua", "ok", "lấy cái này"), hãy chuyển sang chế độ thu thập thông tin. Hãy hỏi thông tin: [Tên, SĐT, Địa chỉ].
    - **LƯU Ý ĐẶC BIỆT:** Nếu bạn vừa đặt câu hỏi xin thông tin (Tên, SĐT, Địa chỉ) và khách hàng trả lời, HÃY BỎ QUA việc Context sản phẩm bị rỗng. Hãy trích xuất thông tin khách vừa đưa.
    - Nếu khách đồng ý chốt đơn, đừng chỉ nói mồm. Hãy kích hoạt action "add_to_cart" trong JSON.
    - Quy trình hỏi: Hỏi Tên & SĐT trước -> Hỏi Địa chỉ sau -> Xác nhận tổng tiền -> Kích hoạt action "add_to_cart" trong JSON.

ĐỊNH DẠNG OUTPUT JSON (BẮT BUỘC):
{
    "text": "Câu trả lời của bạn với khách hàng (Dùng icon ✨, 📱, 🚀 cho sinh động)...",
    "recommended_products": [ID_SP_1, ID_SP_2], // Chỉ điền ID nếu đang giới thiệu sản phẩm
    
    // Trạng thái đơn hàng
    "order_status": "browsing", // Các trạng thái: "browsing" (đang xem), "collecting_info" (đang lấy thông tin), "completed" (xong)

    // Chỉ điền dữ liệu này khi action = "add_to_cart"
    "cart_data": {
        "product_id": 123,   // ID sản phẩm khách chốt (Lấy từ lịch sử chat hoặc context)
        "quantity": 1,
        "customer_info": {
            "name": "Nguyễn Văn A",
            "phone": "0987...",
            "address": "Hà Nội..."
        }
    }
}
"""

def get_current_key():
    if not API_KEYS: return None
    return API_KEYS[current_key_index]

def rotate_key():
    global current_key_index
    if not API_KEYS: return None
    current_key_index = (current_key_index + 1) % len(API_KEYS)
    return API_KEYS[current_key_index]

def configure_genai(key):
    if key: genai.configure(api_key=key)

configure_genai(get_current_key())

@app.route('/process-chat', methods=['POST'])
def process_chat():
    try:
        data = request.json
        message = data.get('message', '')
        history = data.get('history', []) 
        products_context = data.get('products_context', [])

        # Chuyển đổi history thành dạng text để đưa vào Prompt (Giúp AI nhớ ngữ cảnh tốt hơn)
        history_str = ""
        gemini_history = []
        for msg in history:
            role_label = "Khách" if msg['sender'] == 'user' else "Bot"
            history_str += f"{role_label}: {msg['message']}\n"
            # History cho Gemini object
            role = 'user' if msg['sender'] == 'user' else 'model'
            gemini_history.append({'role': role, 'parts': [msg['message']]})

        # Inject dữ liệu vào Prompt
        context_str = json.dumps(products_context, ensure_ascii=False, indent=2)
        system_instruction = SYSTEM_PROMPT_TEMPLATE.replace("{products_context}", context_str)
        system_instruction = system_instruction.replace("{chat_history}", history_str)

        max_retries = 3
        attempt = 0

        while attempt < max_retries:
            try:
                configure_genai(get_current_key())
                model = genai.GenerativeModel(
                    MODEL_NAME,
                    system_instruction=system_instruction,
                    generation_config=GENERATION_CONFIG
                )

                chat = model.start_chat(history=gemini_history)
                response = chat.send_message(message)
                
                return jsonify(json.loads(response.text))

            except exceptions.ResourceExhausted:
                wait_time = 2 ** (attempt + 1)
                print(f"⚠️ Quota exceeded. Rotating key and waiting {wait_time}s...")
                rotate_key()
                time.sleep(wait_time)
                attempt += 1
            except Exception as e:
                print(f"❌ Error: {str(e)}")
                rotate_key()
                attempt += 1
                time.sleep(1)

        return jsonify({
            "text": "Hệ thống đang bận, vui lòng thử lại sau giây lát! 😓",
            "recommended_products": [],
            "order_status": "browsing"
        })

    except Exception as e:
        print(f"🔥 Server Error: {str(e)}")
        return jsonify({"text": "Lỗi kết nối AI.", "recommended_products": []}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)