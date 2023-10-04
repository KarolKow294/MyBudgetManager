let editCategoryButton = document.querySelector("#edit-expense-category-button");
let editCategoryField = document.querySelector("#edit-expense-input-field");

editCategoryButton.addEventListener("click", async () => {
    let editCategoryFieldValue = editCategoryField.value;
    inputLimitInField(editCategoryFieldValue);
})

const getLimitForCategory = async (id) => {
    try {
        const res = await fetch(`../api/limit/${id}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log(`ERROR`, e);
    }
}

const inputLimitInField = async (editCategoryFieldValue) => {
    try {
        const result = await getLimitForCategory(editCategoryFieldValue);
        if (result > 0) {
            document.querySelector("#input-limit-settings").value = result;
        }
    } catch (e) {
        console.log(`ERROR`, e);
    }
}

$("#edit-income-category-button").click(function () {
    let id = $("#edit-income-input-field").val();
    $("#edit-income-category-id").val(id);
});



$("#edit-expense-category-button").click(function () {
    let id = $("#edit-expense-input-field").val();
    $("#edit-expense-category-id").val(id);
});



$("#edit-payment-method-button").click(function () {
    let id = $("#edit-payment-input-field").val();
    $("#edit-payment-method-id").val(id);
});