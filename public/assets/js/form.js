function updateWordCounter(textarea, wordsCounter) {
    const numberOfWords = calculateWords(textarea);
    wordsCounter.innerHTML = numberOfWords + " words";
}

function calculateWords(scriptField) {
    // match any whitespace, not just space literal
    const words = scriptField.value.trim().split(/\s+/);
  
    // if string is empty return 0 or return the number of words
    return words.length === 1 && words[0] === '' ? 0 : words.length;
}

function hideErrors() {
    document.querySelectorAll("div.error-message").forEach(node => node.remove());
    document.querySelectorAll(".has-error").forEach(node => node.classList.remove("has-error"));
}

function showError(field, message) {
    if (document.querySelector("div.error-message[for='" + field + "']") !== null) return;
    let errorField = document.querySelector("[name='" + field + "']");
    if(errorField.type == "radio") {
        errorField = errorField.closest(".radio-selection-box-container");
    }

    errorField.classList.add("has-error");
    errorField.insertAdjacentHTML('afterend', "<div class='error-message' for='" + field + "'>" + message + "</div>");
}

window.addEventListener("load", function() {
    // fields 
    const resetButton = document.querySelector("input[type='reset']");
    const nameField = document.querySelector("input[name='project_name']");
    const countryField = document.querySelector("select[name='country']");
    const provinceField = document.querySelector("select[name='province']");
    const fileField = document.querySelector("input#file-upload");
    const scriptField = document.querySelector("textarea[name='script']");
    // default province field
    const defaultProvinceValue = provinceField.value;
    // default country field
    const defaultCountryValue = countryField.value;
    // allowed file types
    const allowedFileTypes = ["txt", "jpg", "pdf", "png", "jpeg", "md", "doc", "docx", "rtf"];

    // word counter
    const wordsCounter = document.querySelector("#word-counter");
    updateWordCounter(scriptField, wordsCounter);

    // track live and on change in case of paste or selection+delete
    scriptField.addEventListener("keydown", () => updateWordCounter(scriptField, wordsCounter));
    scriptField.addEventListener("change", () => updateWordCounter(scriptField, wordsCounter));

    // function to reset form
    function resetForm() {
        hideErrors();
        document.querySelectorAll(".selected").forEach(node => node.classList.remove("selected"));
        const province = document.querySelector("select[name='province']");
        province.innerHTML = "<option disabled selected>" + defaultProvinceValue + "</option>";
        document.querySelectorAll("select").forEach(node => node.classList.add("initial"));
    }

    // add functionality to allow click anywhere on .budget-selection-box
    document.querySelectorAll(".radio-selection-box").forEach(node => {
        node.onclick = function() {
            // find select
            const formInputElement = node.querySelector("input[type=radio]");
            if(formInputElement) {
                formInputElement.click();
            }
        };
    });

    // add border to box when input radio button clicked
    document.querySelectorAll("input[type='radio']").forEach(node => {
        node.addEventListener("click", function(e) {
            document.querySelectorAll('.radio-selection-box').forEach(node => {
                node.classList.remove("selected");
            })
            
            if (node.checked) {
                node.closest('.radio-selection-box').classList.add('selected');
            }
        });
    });

    // select remove initial class on click
    document.querySelectorAll("select").forEach(node => {
        node.addEventListener("click", function(e) {
            document.querySelectorAll("select").forEach(node2 => {
                node2.classList.remove("initial");
            })
        })
    })

    // update provinces based on country
    countryField.addEventListener("change", function(e) {
        const country = e.target.value; 
        // load province data
        fetch('/assets/data/provinces.json')
        .then(response => response.json())
        .then(data => {
            provinceField.innerHTML = '';

            data["countries"][country].forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceField.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading the JSON file:', error);
        });
    });

    // reset button: remove selected attributes from radio divs, 
    // reset state/province, readd initial to selects
    resetButton.addEventListener("click", (e) => resetForm());

    // submit
    document.querySelector("form").addEventListener("submit", function(e) {
        e.preventDefault();
        let errors = {};

        // validators for form elements
        // name: required, max <300
        if(nameField.value.length === 0) {
            errors["project_name"] = "Please enter a script name for your project.";
        } else if(nameField.value.length > 300 || nameField.value.length === 0) {
            errors["project_name"] = "Please enter a script name field of less than 300 characters.";
        }

        // script: max <2000
        if(scriptField.value.length > 4000) {
            errors["script"] = "Please enter a script with less than 4,000 characters.";
        }

        // country
        if(countryField.value === defaultCountryValue) {
            errors["country"] = "Please select a country";
        }

        // state
        if(provinceField.value === defaultProvinceValue) {
            errors["province"] = "Please select a state/province.";
        }

        // file: format by type
        // this isn't very secure but we should perform
        // more checks on backend side to verify the file
        // is safe
        const fileField = document.querySelector("input#file-upload");
        if (fileField && fileField.files.length > 0) {
            const file = fileField.files[0];
            const fileExtension = file.name.slice(((file.name.lastIndexOf(".") - 1) >>> 0) + 2).toLowerCase();
            if (!allowedFileTypes.includes(fileExtension)) {
                errors["file"] = "You can only upload one of the following types of files: " + allowedFileTypes.join(", ");
            }
        }
        
        // budget
        const budgetField = document.querySelector("input[name='budget']:checked");
        if (budgetField === null) {
            errors["budget"] = "Please select a budget for your project.";
        }

        if(Object.keys(errors).length > 0) {
            hideErrors();
            Object.entries(errors).forEach(([field, message]) => {
                showError(field, message)
            });

            return;
        }

        const formData = new FormData(this);
        fetch("/", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === "success") {
                alert(data.message);
                resetButton.click();
            } else {
                if(data.errors) {
                    for(let errorField in data.errors) {
                        showError(errorField, data.errors[errorField]);
                    }
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => alert("An error occured: " + error));
    })
});