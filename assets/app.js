
import './vendor/bootstrap/bootstrap.index.js';
import './vendor/bootstrap/dist/css/bootstrap.min.css';
import './bootstrap.js';
import Modal from 'bootstrap';


import './styles/app.css';



document.addEventListener('DOMContentLoaded', function() {
    var checkbox1 = document.getElementById('search_isInscrit');
    var checkbox2 = document.getElementById('search_isNotInscrit');

    if (checkbox1 && checkbox2) {
        checkbox1.addEventListener('change', function() {
            if (this.checked) {
                checkbox2.disabled = true;
            } else {
                checkbox2.disabled = false;
            }
        });

        checkbox2.addEventListener('change', function() {
            if (this.checked) {
                checkbox1.disabled = true;
            } else {
                checkbox1.disabled = false;
            }
        });
    }
});