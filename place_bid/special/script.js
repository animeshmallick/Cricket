function fill_special_question(question_id, update_options){
    const urlParams = new URLSearchParams(window.location.search);
    const series_id = getCookie('series_id');
    const match_id = getCookie('match_id');
    const room = urlParams.get('room');
    const amount = document.getElementById('bidSlider').value;

    const url = `${window.location.protocol}//${window.location.hostname}/Cricket/internal/GetSpecialSlotDetails.php?match_id=${match_id}&series_id=${series_id}&question_id=${question_id}&room=${room}&amount=${amount})`;
    fetch(url)
    .then(response => response.json())
    .then(data => {
        fill_special_question_data(data, update_options);
        setTimeout(() => fill_special_question(question_id, false), 5000);
    });
}
function fill_special_question_data(data, update_options){
    document.getElementById("question_name").innerHTML = data.question;
    let amount = document.getElementById('bidSlider').value;
    let slots = document.getElementById('slots');
    let selected_slot = document.querySelector('input[name="option"]:checked');
    slots.innerHTML = '';

    data.options.forEach((option, index) => {
        const optionOuterDiv = document.createElement('div');
        optionOuterDiv.classList.add('slot');
        optionOuterDiv.id = `slot_${index+1}`;
        optionOuterDiv.onclick = changeOptionsUI;
        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'option';
        radio.id = `option_${index+1}`;
        radio.value = index;
        radio.style.display = 'none';
        optionOuterDiv.appendChild(radio);

        let span = document.createElement('span');
        span.classList.add('slot-line');
        span.classList.add('slot-runs');
        span.id = `slot-${index+1}-runs`;
        span.innerHTML = option;
        optionOuterDiv.appendChild(span);

        span = document.createElement('span');
        span.classList.add('slot-line');
        span.innerHTML = `<span class="slot-line">Put <span class="amount-span" id="slot_${index+1}_amount_put">${amount}</span></span>`
        optionOuterDiv.appendChild(span);

        span = document.createElement('span');
        span.classList.add('slot-line');
        span.innerHTML = `<span class="slot-line">Get <span class="amount-span" id="slot_${index+1}_amount_get">${Math.trunc((1 + data.rates[index]) * amount)}</span></span>`
        optionOuterDiv.appendChild(span);

        slots.appendChild(optionOuterDiv);
    });
    if(update_options){
        let max_rate = -1, max_rate_index = -1;
        data.rates.forEach((rate, index) => {
            if(rate > max_rate){
                max_rate = rate;
                max_rate_index = index;
            }
        });
        slots.children[max_rate_index].click();
    }else{
        slots.children[selected_slot.id.split('_')[1]-1].click();
    }
    console.log('Options updated');
}