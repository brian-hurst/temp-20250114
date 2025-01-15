import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    static values = {
        id: String,
        token: String,
    };

    connect() {
        console.log('connected');
    }

    save(event) {

        const formData = new FormData();
        formData.append('_token', this.tokenValue);
        formData.append('saved_items[item]', this.idValue);

        fetch('/saved/items/save', {
            method: 'POST',
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                alert(data.message);
            })
            .catch((error) => {
                console.error("There was a problem with the fetch operation:", error);
            });
    }
}
