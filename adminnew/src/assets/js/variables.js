document.addEventListener("DOMContentLoaded", function(event) {
    // Function to update the textarea for the corresponding row
    function updateTextarea(rowDiv) {
        var attributesArray = [];
        rowDiv.querySelectorAll('.attribute').forEach(function(attributeDiv) {
            var name = attributeDiv.querySelector('.attribute-name').value;
            var value = attributeDiv.querySelector('.attribute-value').value;
            attributesArray.push({ name: name, value: value });
        });
        rowDiv.querySelector('.attributesTextarea').value = JSON.stringify(attributesArray, null, 2);
    }

    document.getElementById('addRowBtn').addEventListener('click', function() {
        var templateRow = document.querySelector('.template-row');
        var cloneRow = templateRow.cloneNode(true);
        cloneRow.classList.remove('template-row');
        cloneRow.classList.add('variable-main');
        cloneRow.querySelector('.attribute').remove();
        cloneRow.style.display = 'flex'; // Show the cloned row
        document.getElementById('dynamicRows').appendChild(cloneRow);

        // Add Row delete button functionality
        // var deleteRowButton = cloneRow.querySelector('.deleteRowButton');
        // deleteRowButton.addEventListener('click', function() {
        //     document.getElementById('dynamicRows').removeChild(cloneRow);
        //     updateTextarea(cloneRow);
        // });

        // Add event listener to the newly added 'Add Attr.' button
        // cloneRow.querySelector('.addAttributeBtn').addEventListener('click', function() {
        //     var templateAttribute = document.querySelector('.template-row .attribute');
        //     var cloneAttribute = templateAttribute.cloneNode(true);
        //     cloneAttribute.style.display = 'block'; // Show the cloned attribute
        //     cloneRow.querySelector('.dynamicAttributes > .row').appendChild(cloneAttribute);

        //     // Update textarea for this row
        //     updateTextarea(cloneRow);
            
        //     // Add input listeners to the cloned attribute
        //     cloneAttribute.querySelector('.attribute-name').addEventListener('blur', function() {
        //         // Convert to lowercase
        //         this.value = this.value.trim().toLowerCase();
        //         updateTextarea(cloneRow);
        //     });
        //     cloneAttribute.querySelector('.attribute-value').addEventListener('blur', function() {
        //         this.value = this.value.trim().toLowerCase();
        //         updateTextarea(cloneRow);
        //     });

        //     // Focus on attribute-name input
        //     cloneAttribute.querySelector('.attribute-name').focus();
            
        //     // Add Attribute delete button functionality
        //     // var deleteAttributeButton = cloneAttribute.querySelector('.deleteAttributeButton');
        //     // deleteAttributeButton.addEventListener('click', function() {
        //     //     cloneRow.querySelector('.dynamicAttributes').removeChild(cloneAttribute);
        //     //     updateTextarea(cloneRow);
        //     // });
        // });

        // Getting variable-main class names list
        const variablesLength = document.getElementsByClassName('variable-main').length;
        const cloneRowInputFile_Name = cloneRow.querySelector("input[type='file']").getAttribute('name').replace("0", variablesLength);
        cloneRow.querySelector("input[type='file']").setAttribute('name', cloneRowInputFile_Name);
    });

    // // Add input listeners to the cloned attribute
    // document.getElementsByClassName('attribute-name').forEach(function(textbox) {
    //     textbox.addEventListener('input', function() {
    //         // Convert to lowercase
    //         this.value = this.value.toLowerCase();
    //         updateTextarea(cloneRow);
    //     });
    // });

    // cloneAttribute.querySelector('.attribute-value').addEventListener('input', function() {
    //     updateTextarea(cloneRow);
    // });

    // Function to add event listeners to existing attributes
    // function addEventListenersToExistingAttributes() {
        document.querySelectorAll('.attribute-name, .attribute-value').forEach(function(input) {
            input.addEventListener('blur', function() {
                // Convert attribute name to lowercase if it's an attribute name input
                // if (input.classList.contains('attribute-name')) {
                    input.value = input.value.trim().toLowerCase();
                // } else {
                //     input.value = input.value.trim();
                // }
                updateTextarea(input.closest('.variable-row'));
            });
        });
    // }

    // Add event listeners to existing attributes when the page loads
    // addEventListenersToExistingAttributes();

    // Function to add event listeners to attribute inputs
    function addEventListenersToAttributes(row) {
        row.querySelectorAll('.attribute-name, .attribute-value').forEach(function(input) {
            input.addEventListener('blur', function() {
                // Convert attribute name to lowercase if it's an attribute name input
                // if (input.classList.contains('attribute-name')) {
                    input.value = input.value.trim().toLowerCase();
                // } else {
                //     input.value = input.value.trim();
                // }
                updateTextarea(row);
            });
        });
    }

    // Add event listener to the 'Add Attribute' buttons using event delegation
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('addAttributeBtn')) {
            var templateAttribute = document.querySelector('.template-row .attribute');
            var row = event.target.closest('.row');
            var cloneAttribute = templateAttribute.cloneNode(true);
            cloneAttribute.style.display = 'block'; // Show the cloned attribute
            row.querySelector('.dynamicAttributes > .row').appendChild(cloneAttribute);

            // Update textarea for this row
            updateTextarea(row);

            // Add event listeners to the cloned attribute
            addEventListenersToAttributes(row);
        }
    });
});