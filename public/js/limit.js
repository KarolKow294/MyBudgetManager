let categoryField = document.querySelector("#category-field");


categoryField.addEventListener("change", async () => {
    let categoryFieldValue = categoryField.value;
    renderInfoBox(categoryFieldValue);
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

const getTotalForCategory = async (id) => {
    try {
        const res = await fetch(`../api/total/${id}`);
        const data = await res.json();
        return data;
    } catch (e) {
        console.log(`ERROR`, e);
    }
}

const renderInfoBox = async (categoryFieldValue) => {
    try {
        const result = await getLimitForCategory(categoryFieldValue);
        let limitInfo = `Ta kategoria podlega miesięcznemu limitowi na kwotę ${result} zł.`;
        document.querySelector("#limit-info").innerHTML = limitInfo;
    } catch (e) {
        console.log(`ERROR`, e);
    }
}





/*$("#categoryField").on("change", function() {
    $.ajax("/Budget/limit/").done(function(data) {
        $("#limit-info").html(data);
    });
});*/