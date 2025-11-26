import './bootstrap';

import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import collapse from '@alpinejs/collapse'

window.Alpine = Alpine;
window.Sortable = Sortable;

Alpine.plugin(collapse);

Alpine.start();
