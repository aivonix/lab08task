document.addEventListener("DOMContentLoaded", function() {
    var myButton = document.getElementById("has_discount");
    var discountCard = document.querySelector(".discountCard");

    myButton.addEventListener("click", function() {
        var discountCardInput = document.getElementById("discount_card");

        if (myButton.checked) {
            discountCard.classList.remove("hidden");
            discountCardInput.setAttribute("required", "required"); // Make the input required
        } else {
            discountCard.classList.add("hidden");
            discountCardInput.removeAttribute("required"); // Remove the required attribute
        }
    });
});