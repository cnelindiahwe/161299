$(function(){
    
    const invoice_add = document.getElementById('invoice-add-btn');
    const invoiceUl = document.querySelector('ul li.invoiceto');
    if(invoice_add){
        invoice_add.addEventListener('click', (event) => {
            let email_to = $('#invoice-email').val();
            let invoice_mail = $('#invoice-add').val();
            if(invoiceUl){
                const ulhtml = '<li class="item checked invoiceto"><span class="item-text">'+invoice_mail+'</span></li>';
                $('.invoiceUl').prepend(ulhtml);
                const newItem = $('.invoiceUl li').first()[0];
                newItem.addEventListener("click", () => {
                    newItem.classList.toggle("checked");
                    updateEmailField();
                });
            }
            else{
                const ulhtml = '<li class="item checked invoiceto"><span class="item-text">'+invoice_mail+'</span></li>';
                $('.invoiceUl').html(ulhtml);
                const newItem = $('.invoiceUl li').first()[0];
                newItem.addEventListener("click", () => {
                    newItem.classList.toggle("checked");
                    updateEmailField();
                });
            }
            if(email_to){
                let newmail = email_to+','+invoice_mail;
                $('#invoice-email').val(newmail);
                // const ulhtml = '<li class="item checked invoiceto"><span class="item-text">'+invoice_mail+'</span></li>';
                // $('.invoiceUl').append(ulhtml);
                $('#invoice-add').val('');
                
            }
            else{
                $('#invoice-email').val(invoice_mail);
                // const ulhtml = '<li class="item checked invoiceto"><span class="item-text">'+invoice_mail+'</span></li>';
                // $('.invoiceUl').html(ulhtml);
                $('#invoice-add').val('');
            }
        
        });
    }
    
    const  items = document.querySelectorAll(".invoiceto");
    
    if(items){
        items.forEach(item => {
            item.addEventListener("click", () => {
                item.classList.toggle("checked");
                updateEmailField();
            })
        });
    }
    
    
    
    const ccitems = document.querySelectorAll(".ccto");
    if(ccitems){
        ccitems.forEach(item => {
            item.addEventListener("click", () => {
                item.classList.toggle("checked");
                updateCCEmailField();
                // ccitems.forEach(ccitem => {
                // 	ccitem.classList.remove("checked");
                // });
        
                // Toggle the "checked" class for the clicked item
                // item.classList.toggle("checked");
        
                // // Find the checked item and update the #invoice-email-cc input
                // const checkedItem = document.querySelector(".ccto.checked");
                // if (checkedItem) {
                // 	const itemText = checkedItem.querySelector(".item-text").textContent;
                // 	$('#invoice-email-cc').val(itemText);
                // } else {
                // 	// If no item is checked, clear the #invoice-email-cc input
                // 	$('#invoice-email-cc').val('');
                // }
    
            })
        });
    }
    
    const checkboxes = document.querySelectorAll('input[name="template"]');
    if(checkboxes){
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('change', () => {
        checkboxes.forEach((otherCheckbox) => {
          if (otherCheckbox !== checkbox) {
            otherCheckbox.checked = false;
          }
        });
        if (checkbox.checked) {
            let dataId = $(checkbox).data('id');
            let subject = $(checkbox).data('subject');
            let emailBody = $('input[name="' + dataId + '"]').val();
            $('#bodyContent').val(emailBody);
            $('input[name="subject"]').val(subject);
          }
      });
    });
    
    }
    
    function updateEmailField() {
        const  items = document.querySelectorAll(".invoiceto");
        const checkedItemValues = [];
        items.forEach(item => {
            // Check if the item has the "checked" class
            if (item.classList.contains("checked")) {
                // Get the text content of the item and add it to the array
                const itemText = item.querySelector(".item-text").textContent;
                checkedItemValues.push(itemText);
            }
        });
        const SeparatedValues = checkedItemValues.join(','); 
        $('#invoice-email').val(SeparatedValues);
    }
    function updateCCEmailField() {
        const  items = document.querySelectorAll(".ccto");
        const checkedItemValues = [];
        items.forEach(item => {
            // Check if the item has the "checked" class
            if (item.classList.contains("checked")) {
                // Get the text content of the item and add it to the array
                const itemText = item.querySelector(".item-text").textContent;
                checkedItemValues.push(itemText);
            }
        });
        const SeparatedValues = checkedItemValues.join(','); 
        $('#invoice-email-cc').val(SeparatedValues);
    }

})