$.validator.addMethod('validPassword',
        function(value, element, param) {
            if (value != '') {
                if (value.match(/.*[a-z]+.*/i) == null) {
                    return false;
                }
                if (value.match(/.*\d+.*/) == null) {
                    return false;
                }
            }
            return true;
        },
        'Must contain at least one letter and one number'
);

$("#delete-user").on("click", function(e) {
    e.preventDefault();

    if (confirm("Czy chcesz na pewno usunąć konto?")) {
        let frm = $("<form>");
        frm.attr('method', 'post');
        frm.attr('action', $(this).attr('href'));
        frm.appendTo("body");
        frm.submit();
    }
});

$("#delete-income-category").on("click", function(e) {
    if (!confirm("Czy chcesz na pewno usunąć kategorię przychodu?")) {
        e.preventDefault();
    }
});

$("#delete-expense-category").on("click", function(e) {
    if (!confirm("Czy chcesz na pewno usunąć kategorię wydatku?")) {
        e.preventDefault();
    }
});

$("#delete-payment-method").on("click", function(e) {
    if (!confirm("Czy chcesz na pewno usunąć metodę płatności?")) {
        e.preventDefault();
    }
});

$("#delete-income").on("click", function(e) {
    e.preventDefault();

    if (confirm("Czy chcesz na pewno usunąć przychód?")) {
        var frm = $("<form>");
        frm.attr('method', 'post');
        frm.attr('action', $(this).attr('href'));
        frm.appendTo("body");
        frm.submit();
    }
});

$("#delete-expense").on("click", function(e) {
    e.preventDefault();

    if (confirm("Czy chcesz na pewno usunąć wydatek?")) {
        var frm = $("<form>");
        frm.attr('method', 'post');
        frm.attr('action', $(this).attr('href'));
        frm.appendTo("body");
        frm.submit();
    }
});