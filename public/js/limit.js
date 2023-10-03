let categoryField = document.querySelector("#category-field");
let dateField = document.querySelector("#date-field");

categoryField.addEventListener("change", async () => {
    let categoryFieldValue = categoryField.value;
    renderInfoBox(categoryFieldValue);
    let dateFieldValue = dateField.value;
    renderExpenseBox(categoryFieldValue, dateFieldValue);
})

dateField.addEventListener("change", async () => {
    let categoryFieldValue = categoryField.value;
    let dateFieldValue = dateField.value;
    if (categoryFieldValue) {
        renderExpenseBox(categoryFieldValue, dateFieldValue);
    }
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

const getTotalForCategory = async (id, date) => {
    try {
        const res = await fetch(`../api/total/${id}?date=${date}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log(`ERROR`, e);
    }
}

const renderInfoBox = async (categoryFieldValue) => {
    try {
        const result = await getLimitForCategory(categoryFieldValue);
        let limitInfo = '';
        if (result == '0.00') {
            limitInfo = `Ta kategoria nie posiada limitu.`;
        } else {
            limitInfo = `Ta kategoria podlega miesięcznemu limitowi na kwotę ${result} zł.`;
        }     
        document.querySelector("#limit-info").innerHTML = limitInfo;
    } catch (e) {
        console.log(`ERROR`, e);
    }
}

const renderExpenseBox = async (categoryFieldValue, dateFieldValue) => {
    try {
        const result = await getTotalForCategory(categoryFieldValue, dateFieldValue);
        if (result) {
            document.querySelector("#expense-in-current-month").innerHTML = result;
        } else {
            document.querySelector("#expense-in-current-month").innerHTML = "Nie masz wydatków w danym miesiącu.";
        }
        
    } catch (e) {
        console.log(`ERROR`, e);
    }
}
