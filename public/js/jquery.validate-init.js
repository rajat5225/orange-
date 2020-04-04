var fixedLength = 0;
jQuery.validator.addMethod("filesize_max", function(value, element, param) {
    var isOptional = this.optional(element),
        file;
    
    if(isOptional) {
        return isOptional;
    }
    
    if ($(element).attr("type") === "file") {
        
        if (element.files && element.files.length) {
            
            file = element.files[0];      
            //console.log(file.size);      
            return ( file.size && file.size <= 52428800 ); 
        }
    }
    return false;
}, "File size is too large.");

jQuery.validator.addMethod("fixedDigits", function(value, element, param) {
    var isOptional = this.optional(element);
    fixedLength = param;

    if(isOptional) {
        return isOptional;
    }
    
    return ($(element).val().length <= param);
}, function() {return "Value cannot exceed "+fixedLength+" characters."});

jQuery.validator.addMethod("extension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select image with a valid extension (.jpg, .jpeg, .png, .gif, .svg)");

jQuery.validator.addMethod("docextension", function(value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select file with a valid extension (.jpg, .jpeg, .png, .doc, .docx, .pdf)");

jQuery.validator.addMethod("decimalPlaces", function(value, element) {
    return this.optional(element) || /^\d+(\.\d{0,2})?$/i.test(value);
}, "Please enter a value with maximum two decimal places.");

jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
}, "Please enter alphanumeric value.");

jQuery.validator.addMethod("exactlength", function(value, element, param) {
 return this.optional(element) || value.length == param;
}, $.validator.format("Please enter exactly {0} characters."));

jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Name can have alphabets and space only.");

jQuery.validator.addMethod("correctPassword", function(value, element) {
  return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{6,}$/i.test(value);
}, "Please fill minimum 6 character Password with uppercase, lowercase, special character and digit");

jQuery.validator.addMethod("vehicle_regno", function(value, element) {
  return this.optional(element) || /^[A-Za-z]{2}[ -]{0,1}[0-9]{1,2}(?: [A-Za-z])?(?: [A-Za-z]*)? [0-9]{4}$/i.test(value);
}, "Please enter valid Registration Number");

jQuery.validator.addMethod("vehicle_plate", function(value, element) {
  return this.optional(element) || /^[A-Za-z]{2}[ -]{0,1}[0-9]{1,2}(?: [A-Za-z0-9])?(?: [A-Za-z0-9]*)? [0-9]{1,4}$/i.test(value);
}, "Please enter valid Registration Number");

var form_validation = function() {
    var e = function() {
            jQuery(".form-valide").validate({
                ignore: [".note-editor *", "val-pass"],
                errorClass: "invalid-feedback animated fadeInDown",
                errorElement: "div",
                errorPlacement: function(e, a) {
                    jQuery(a).parents(".form-group").append(e)
                },
                highlight: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
                },
                success: function(e) {
                    jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
                },
                rules: {
                    "val-username": {
                        required: !0,
                        minlength: 3
                    },
                    "val-email": {
                        required: !0,
                        email: !0
                    },
                    "val-pass": {
                        required: !0
                    },
                    "val-password": {
                        required: !0,
                        minlength: 6,
                        correctPassword: !0
                    },
                    "val-confirm-password": {
                        required: !0,
                        equalTo: "#val-password",
                        correctPassword: !0
                    },
                    "val-select2": {
                        required: !0
                    },
                    "val-select2-multiple": {
                        required: !0,
                        minlength: 2
                    },
                    "val-suggestions": {
                        required: !0,
                        minlength: 5
                    },
                    "val-skill": {
                        required: !0
                    },
                    "val-currency": {
                        required: !0,
                        currency: ["$", !0]
                    },
                    "val-website": {
                        required: !0,
                        url: !0
                    },
                    "val-phoneus": {
                        required: !0,
                        phoneUS: !0
                    },
                    "val-digits": {
                        required: !0,
                        digits: !0
                    },
                    "val-number": {
                        required: !0,
                        number: !0
                    },
                    "val-range": {
                        required: !0,
                        range: [1, 5]
                    },
                    "val-terms": {
                        required: !0
                    },
                    "val-name": {
                        required: !0,
                        maxlength: 20
                    },
                    "val-capacity": {
                        required: !0,
                        digits: !0,
                        maxlength: 10
                    },
                    "val-applies": {
                        required: !0,
                        digits: !0,
                        maxlength: 10
                    },
                    "val-rides": {
                        digits: !0,
                        maxlength: 10
                    },
                    "val-minRides": {
                        digits: !0,
                        maxlength: 10
                    },
                    "val-price": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-amt": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-discount": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-driver": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10,
                        max: 100
                    },
                    "val-distance": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-waiting": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-cancel": {
                        required: !0,
                        number: !0,
                        decimalPlaces: !0,
                        maxlength: 10
                    },
                    "val-image": {
                        required: !0,
                        extension: "jpeg|png|jpg|gif|svg",
                        // filesize_max: !0
                    },
                    "doc[]": {
                        required: !0,
                        docextension: "jpeg|png|jpg|doc|docx|pdf",
                    },
                    "val-code": {
                        required: !0,
                        alphanumeric: !0,
                        fixedDigits: 6
                    },
                    "val-title": {
                        required: !0,
                        maxlength: 200
                    },
                    "val-description": {
                        required: !0
                    },
                    "val-couponTerms": {
                        required: !0
                    },
                    "val-type": {
                        required: !0
                    },
                    "val-amounttype": {
                        required: !0
                    }
                },
                messages: {
                    "val-username": {
                        required: "Please enter a username",
                        minlength: "Your username must consist of at least 3 characters"
                    },
                    "val-email": "Please enter a valid email address",
                    "val-password": {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 6 characters long"
                    },
                    "val-confirm-password": {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 6 characters long",
                        equalTo: "Please enter the same password as above"
                    },
                    "val-select2": "Please select a value!",
                    "val-select2-multiple": "Please select at least 2 values!",
                    "val-suggestions": "What can we do to become better?",
                    "val-skill": "Please select a skill!",
                    "val-currency": "Please enter a price!",
                    "val-website": "Please enter your website!",
                    "val-phoneus": "Please enter a US phone!",
                    "val-digits": "Please enter only digits!",
                    "val-number": "Please enter a number!",
                    "val-range": "Please enter a number between 1 and 5!",
                    "val-terms": "You must agree to the service terms!",
                    "val-capacity": {
                        digits: "Please enter valid whole number"
                    }
                }
            })
        }
    return {
        init: function() {
            e(), jQuery(".js-select2").on("change", function() {
                jQuery(this).valid()
            })
        }
    }
}();
jQuery(function() {
    form_validation.init()
});