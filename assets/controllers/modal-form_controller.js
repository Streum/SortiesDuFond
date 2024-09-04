import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['modal'];
    static values = {
        formUrl: String,
    }

    async submitForm(event) {
        console.log('gd');
        const form = document.querySelector('.modal-body form');
        const formData = new FormData(form);
        try {
            const response = await fetch('/lieux/create_ajax', {
                method: form.method,
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                document.querySelector(".modal-body").innerHTML = errorText;
                return;
            }

            const result = await response.json();
            const select = document.getElementById('sorties_noLieu');
            select.options[select.length] = new Option(result.name, result.id, true, true);
            let modal = document.querySelector('#formModal');
            Modal.getInstance(modal).hide();
        } catch (error) {
            console.error('Error:', error);
        }
        event.stopPropagation();
    }

    async openModal(event) {
        const modal = new Modal('#formModal', { keyboard: false });
        modal.show();
        document.querySelector(".modal-body").innerHTML = await (await fetch(this.formUrlValue)).text();
        event.preventDefault();
        event.stopPropagation();
    }
}