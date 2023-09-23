document.addEventListener("DOMContentLoaded", function() {
    let myButton = document.getElementById("has_discount");
    let discountCard = document.querySelector(".discountCard");

    
    let discountCardInput = document.getElementById("discount_card");

    if (myButton.checked) {
        discountCard.classList.remove("hidden");
        discountCardInput.setAttribute("required", "required"); // Make the input required
    } else {
        discountCard.classList.add("hidden");
        discountCardInput.removeAttribute("required"); // Remove the required attribute
    }

    myButton.addEventListener("click", function() {
        let discountCardInput = document.getElementById("discount_card");

        if (myButton.checked) {
            discountCard.classList.remove("hidden");
            discountCardInput.setAttribute("required", "required"); // Make the input required
        } else {
            discountCard.classList.add("hidden");
            discountCardInput.removeAttribute("required"); // Remove the required attribute
        }
    });
});