document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deliveryForm');
    const resultContainer = document.getElementById('resultContainer');
    const calculateBtn = document.getElementById('calculateBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        calculateBtn.disabled = true;
        calculateBtn.textContent = 'Загрузка...';

        fetch('calculate_delivery.php?' + params.toString())
            .then(response => response.json())
            .then(data => {
                displayResult(data);
            })
            .catch(error => {
                displayResult({
                    status: 'error',
                    message: 'Произошла ошибка: ' + error.message
                });
            })
            .finally(() => {
                calculateBtn.disabled = false;
                calculateBtn.textContent = 'Рассчитать';
            });
    });

    function displayResult(data) {
        resultContainer.innerHTML = '';

        const resultElement = document.createElement('div');
        resultElement.className = data.status === 'OK' ? 'result success' : 'result error';
        resultElement.textContent = data.message;

        if (data.status === 'OK') {
            const priceElement = document.createElement('div');
            priceElement.className = 'price';
            priceElement.textContent = `Цена: ${data.price} руб.`;
            resultElement.appendChild(priceElement);
        }

        resultContainer.appendChild(resultElement);
    }
});