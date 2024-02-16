//Function is called to display the settings or display and hide the other div or to hide the current div if it is already display and the button has been clicked
function cn_view_div(pos){
    if($('.cn-view-div').length == 2 && $('.cn-nav-div').length == 2 && (pos == 0 || pos == 1)){
        const div = $('.cn-nav-div')[pos];
        if(pos == 0){
            $(`#cn_rs_form_width`)[0].value = null;
            $(`#cn_rs_form_height`)[0].value = null;
            $(`#cn_rs_form_aspect`)[0].checked = false;
            $(`#cn_rs_form_icons`)[0].value = null;
            $(`#cn_rs_div`)[0].style.display = 'none';
            $('.cn-nav-div')[1].style.display = 'none';
            $(`#cn_rs_success`)[0].style.display = 'none';
            $(`#cn_rs_error`)[0].style.display = 'none';
        } else if(pos == 1){
            $('#cn_rd_form_div')[0].innerHTML = '';
            $(`#cn_rd_error`)[0].style.display = 'none';
            $(`#cn_rd_success`)[0].style.display = 'none';
            $('#cn_rd_div')[0].style.display = 'none';
            $('.cn-nav-div')[0].style.display = 'none';
        }
        div.style.display = (div.style.display == 'block') ? 'none' : 'block';
    }
}
//Function is called when a role setting li element is clicked
function role_settings(id){
    //Define successText variable and set it to display none
    const successText = $(`#cn_rs_success`)[0];
    successText.style.display = 'none';
    //Define errorText variable and set it to display none
    const errorText = $(`#cn_rs_error`)[0];
    errorText.style.display = 'none';
    //Define div variable and replace delete the innerHTML
    const div = $('#cn_rs_div')[0];
    div.style.display = 'none';
    //Send a HTTP post request to get the data for the specified id
    //Define the xhr variable
    const xhr = new XMLHttpRequest();
    //Define the type, path and async
    xhr.open('POST', './classes/inc/get_role_setting.inc.php', true);
    //Set the request header
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //Define the function for when the request is loaded
    xhr.onload = function(){
        if(this.status == 200){
            //Try and parse the response as JSON and if it fails, display an error message
            try{
                //Define text variable and try to parse the response text as json
                const text = JSON.parse(this.responseText);
                //Depending on the response, display an error or display HTML
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['success']){
                    successText.innerText = 'Success';
                    successText.style.display = 'block';
                    $(`#cn_rs_form_h4`)[0].innerText = text['success']['name'];
                    if(text['success']['new']){
                        $(`#cn_rs_form_width`)[0].value = null;
                        $(`#cn_rs_form_height`)[0].value = null;
                        $(`#cn_rs_form_aspect`)[0].checked = false;
                        $(`#cn_rs_form_icons`)[0].value = null;
                    } else if(text['success']['new'] == false){
                        $(`#cn_rs_form_width`)[0].value = text['success']['width'];
                        $(`#cn_rs_form_height`)[0].value = text['success']['height'];
                        $(`#cn_rs_form_aspect`)[0].checked = text['success']['aspect']; 
                        $(`#cn_rs_form_icons`)[0].value = text['success']['icons'];
                    }
                    div.style.display = 'block';
                    //Remove success message after 1 second
                    setTimeout(()=>{
                        $(`#cn_rs_success`)[0].style.display = 'none';
                    },1000);
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } catch{
                //Display invaid response text if a error is thrown
                errorText.innerText = 'Invalid response.';
                errorText.style.display = 'block';
            }
        } else {
            //Display an error when a connection fails
            errorText.innerText = 'Connection error.';
            errorText.style.display = 'block';
        }
    }
    //Send the request
    xhr.send(`id=${id}`);
}
//Add a event listener to the role settings form
$(`#cn_rs_form`)[0].addEventListener('submit', function(e){
    e.preventDefault();
    //Hide error and success text
    errorText = $(`#cn_rs_error`)[0];
    errorText.style.display = 'none';
    successText = $(`#cn_rs_success`)[0];
    successText.style.display = 'none';
    //Define variables for each form input
    const width = $(`#cn_rs_form_width`)[0];
    const height = $(`#cn_rs_form_height`)[0];
    const aspect = $(`#cn_rs_form_aspect`)[0];
    const icons = $(`#cn_rs_form_icons`)[0];
    //Send HTTP post request to set the data for a specific id
    //Set xhr constant
    const xhr = new XMLHttpRequest();
    //Set the type, path and async
    xhr.open('POST', './classes/inc/set_role_setting.inc.php', true);
    //Set the request header
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //Define the function for whne the request is loaded
    xhr.onload = function(){
        if(this.status == 200){
            //Try and parse the response as JSON and if it fails, display an error message
            try{
                //Define text variable and try to parse the response text as json
                const text = JSON.parse(this.responseText);
                //Depending on the response, display an error or success
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['success']){
                    successText.innerText = 'Success';
                    successText.style.display = 'block';
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } catch{
                //Display error stating a invalid response
                errorText.innerText = 'Invalid response.';
                errorText.style.display = 'block';
            }
        } else {
            //Display error stating connection error
            errorText.innerText = 'Connection error.';
            errorText.style.display = 'block';
        }
    }
    //Change boolean value to integer
    aspectVal = (aspect.checked) ? 1 : 0;
    //Send the request
    xhr.send(`width=${width.value}&height=${height.value}&aspect=${aspectVal}&icons=${icons.value}`);
})
//Function is called when a role display li element is clicked
function role_displays(id){
    //Define errorText and hide it
    const errorText = $(`#cn_rd_error`)[0];
    errorText.style.display = 'none';
    //Define successText and hide it
    const successText = $(`#cn_rd_success`)[0];
    successText.style.display = 'none';
    //Define div and hide it
    const div = $(`#cn_rd_div`)[0];
    div.style.display = 'none';
    //Send a HTTP post request to get the data for the specified id
    //Define the xhr variable
    const xhr = new XMLHttpRequest();
    //Define the type, path and async
    xhr.open('POST', './classes/inc/get_role_images.inc.php', true);
    //Set the request header
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //Define the function for when the request is loaded
    xhr.onload = function(){
        if(this.status == 200){
            //Try and parse the response as JSON and if it fails, display an error message
            try{
                //Define text variable and try to parse the response text as json
                const text = JSON.parse(this.responseText);
                //Depending on the response, display an error or display HTML
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['success']){
                    successText.innerText = 'Success';
                    successText.style.display = 'block';
                    $('.cn_rd_form_h4')[0].innerText = $(`#cn_role_display${id}`)[0].innerText;
                    $(`#cn_rd_form_div`)[0].innerHTML = text['success'];
                    div.style.display = 'block';
                    //Remove success message after 1 second
                    setTimeout(()=>{
                        $(`#cn_rd_success`)[0].style.display = 'none';
                    },1000);
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } catch{
                //Display invalid response text if a error is thrown
                errorText.innerText = 'Invalid response.';
                errorText.style.display = 'block';
            }
        } else {
            //Display an error when a connection fails
            errorText.innerText = 'Connection error.';
            errorText.style.display = 'block';
        }
    }
    //Send the request
    xhr.send(`id=${id}`);
}
//Function is called to add a new icon
function cn_new_icon(){
    if($(`.cn-rd-form-img`).length > 0){
        const maxLength = $(`.cn-rd-form-img`).length;
        //Create div element
        const div = $('<div>').attr('class', 'cn-image-div-inner border');
        //create span element
        div.append($('<span>').addClass('c-pointer').attr({onclick: `cn_remove_icon(${maxLength})`}).append($('<b>').text('X')));
        //Create h4 element
        div.append($('<h4>').addClass('text-center').text(`${maxLength + 1}`));
        //Create URL input element
        div.append($('<p>').text('URL: ').append($('<input>').addClass('cn-rd-form-url w-75').attr({type: 'text', required: true})));
        //Create delete image element
        div.append($('<p>').addClass('c-pointer').attr({onclick: `cn_remove_img(${maxLength})`}).append($('<b>').text('X')));
        //Create img element
        imgElement = $(`.cn-rd-form-img`)[0];
        const img = $('<img>').addClass('cn-rd-form-img');
        //Make the image keep the aspect ratio if it is included in the style already
        if(imgElement.getAttribute('style').includes('object-fit:contain;')){
            img.attr({
                style: `width:${imgElement.style.width};height:${imgElement.style.height};object-fit:contain;`,
                src: ''
            });
        } else {
            img.attr({
                style: `width:${imgElement.style.width};height:${imgElement.style.height};`,
                src: ''
            });
        }
        div.append(img);
        //Create image element
        div.append($('<p>').text('Image: ').append($('<input>').addClass('cn-rd-form-image').attr({type: 'file', onchange: `cn_new_file(${maxLength})`})));
        //Create text element
        div.append($('<p>').text('Text: ').append($('<input>').addClass('cn-rd-form-text w-75').attr({type: 'text'})));
        //Create alttext element
        div.append($('<p>').text('Alt text: ').append($('<input>').addClass('cn-rd-form-alttext w-75').attr({type: 'text'})));
        //Append new div
        $(`#cn_rd_form_div`).append(div);
    }
}
//Function is called to load a image when a file is uploaded
function cn_new_file(pos){
    //Define errorText and hide it
    const errorText = $(`#cn_rd_error`)[0];
    errorText.style.display = 'none';
    //Validate the number of elements with specific classes are equal and the pos provided is in the range of the length\
    if(pos < $(`.cn-rd-form-image`).length && pos >= 0 && $(`.cn-rd-form-img`).length == $(`.cn-rd-form-image`).length){
        const file = $(`.cn-rd-form-image`)[pos];
        const fileType = file.value.split('.').pop();
        //Validate whether the file chosen is a image
        if(fileType == 'png' || fileType == 'jpg' || fileType == 'jpeg'){
            //Load the image on the img element
            const fileReader = new FileReader();
            fileReader.readAsDataURL(file.files[0]);
            fileReader.addEventListener("load", function(){
                $(`.cn-rd-form-img`)[pos].setAttribute('src', this.result);
            });
        } else {
            //Remove the file selected if it is not a image and display an error message
            file.value = '';
            errorText.innerText = 'File must be a png or jpg or jpeg';
            errorText.style.display = 'block';
        }
    } else {
        //Display an error message
        errorText.innerText = 'Javascript error when uploading file';
        errorText.style.display = 'block';
    }
}
//Function is called to remove a icon with a specific pos
function cn_remove_icon(pos){
    //Define errorText and hide it
    const errorText = $(`#cn_rd_error`)[0];
    errorText.style.display = 'none';
    //Define successText and hide it
    const successText = $(`#cn_rd_success`)[0];
    successText.style.display = 'none';
    //Validate the number of elements with specific classes to ensure a error isn't thrown when executing javascript
    if($(`.cn-image-div-inner`).length != 1 && pos <= $(`.cn-rd-form-url`).length && pos >= 0 &&$(`.cn-rd-form-url`).length > 0 && $(`.cn-rd-form-image`).length > 0 && $(`.cn-rd-form-text`).length > 0 && $(`.cn-rd-form-alttext`).length > 0 && $(`.cn-rd-form-url`).length == $(`.cn-rd-form-image`).length && $(`.cn-rd-form-alttext`).length == $(`.cn-rd-form-text`).length && $(`.cn-rd-form-image`).length == $(`.cn-rd-form-alttext`).length){
        //Send a remove request and only remove the div if it was succesfully removed from the database
        //Define the xhr variable
        const xhr = new XMLHttpRequest();
        //Define the type, path and async
        xhr.open('POST', './classes/inc/remove_role_images.inc.php', true);
        //Set the request header
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //Define the function for when the request is loaded
        xhr.onload = function(){
            if(this.status == 200){
                try{
                    //Define text variable and try to parse the response text as json
                    const text = JSON.parse(this.responseText);
                    //Depending on the response, display an error or display HTML
                    if(text['error']){
                        errorText.innerText = text['error'];
                        errorText.style.display = 'block';
                    } else if(text['success']){
                        //Remove the selected div
                        $(`.cn-image-div-inner`)[pos].remove();
                        //Change values for h4 and the onchange attribute
                        $(`.cn-image-div-inner`).each(function(index, element){
                            $(element).find('h4')[0].innerText = index+1;
                            $(element).find('.cn-rd-form-image')[0].setAttribute('onchange', `cn_new_file(${index})`);
                            $(element).find('span')[0].setAttribute('onclick', `cn_remove_icon(${index})`);
                        });
                        //Display success message
                        successText.innerText = 'Success';
                        successText.style.display = 'block';
                    } else {
                        errorText.innerText = 'Submit error';
                        errorText.style.display = 'block';
                    }
                } catch{
                    errorText.innerText = 'Invalid response';
                    errorText.style.display = 'block';
                }
            } else {
                //Display invalid response text if a error is thrown
                errorText.innerText = 'Connection error';
                errorText.style.display = 'block';
            }
        }
        //Send the request
        xhr.send(`id=${pos+1}`);
    } else {
        errorText.innerText = ($(`.cn-image-div-inner`).length == 1) ? 'You cannot remove the last icon' : 'Javascript error when delete icon';
        errorText.style.display = 'block';
    }
}
//Function is called to remove the image for a specific icon
function cn_remove_img(pos){
    if(pos < $(`.cn-rd-form-image`).length && pos >= 0 && $(`.cn-rd-form-img`).length == $(`.cn-rd-form-image`).length){
        const file = $(`.cn-rd-form-image`)[pos];
        const img = $(`.cn-rd-form-img`)[pos];
        file.value = '';
        img.src = '';
    } 
}
//Add event listener to the role display form
$(`#cn_rd_form`)[0].addEventListener('submit', function(e){
    e.preventDefault();
    //Define errorText and hide it
    const errorText = $(`#cn_rd_error`)[0];
    errorText.style.display = 'none';
    //Define successText and hide it
    const successText = $(`#cn_rd_success`)[0];
    successText.style.display = 'none';
    //Check if the elements with specific classes exists
    if($(`.cn-rd-form-url`).length > 0 && $(`.cn-rd-form-img`).length > 0 && $(`.cn-rd-form-text`).length > 0 && $(`.cn-rd-form-alttext`).length > 0 && $(`.cn-rd-form-url`).length == $(`.cn-rd-form-img`).length && $(`.cn-rd-form-alttext`).length == $(`.cn-rd-form-text`).length && $(`.cn-rd-form-img`).length == $(`.cn-rd-form-alttext`).length){
        //Define variables which contian the form data
        const url = $(`.cn-rd-form-url`);
        const img = $(`.cn-rd-form-img`);
        const text = $(`.cn-rd-form-text`);
        const alttext = $(`.cn-rd-form-alttext`);
        //Create the request
        let request = new FormData();
        url.each(function(index, element){
            request.append(`url${index}`, element.value);
        });
        img.each(function(index, element){
            if(element.src.includes('://') == false){
                request.append(`img${index}`, element.src);
            } else {
                request.append(`img${index}`, '');
            }
        });
        text.each(function(index, element){
            request.append(`text${index}`, element.value);
        });
        alttext.each(function(index, element){
            request.append(`alttext${index}`, element.value);
        });
        request.append(`total`, url.length);
        //Create xhr constant and its parameters
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/set_role_images.inc.php');
        xhr.onload = function(){
            if(this.status == 200){
                //Try and parse the response as JSON and if it fails, display an error message
                try{
                    //Define text variable and try to parse the response text as json
                    const text = JSON.parse(this.responseText);
                    //Depending on the response, display an error or success
                    if(text['error']){
                        errorText.innerText = text['error'];
                        errorText.style.display = 'block';
                    } else if(text['success']){
                        successText.innerText = 'Success';
                        successText.style.display = 'block';
                    } else {
                        errorText.innerText = 'Submit error';
                        errorText.style.display = 'block';
                    }
                } catch{
                    //Display invalid reponse text if a error is thrown
                    errorText.innerText = 'Invalid response';
                    errorText.style.display = 'block';
                }
            } else {
                //Display an error when the connection fails
                errorText.innerText = 'Connection error';
                errorText.style.display = 'block';
            }
        }
        //send the request
        xhr.send(request);
    } else {
        //Output an error if the required number of elements with the required class is not correct (the same number of elements for each class)
        errorText.innerText = 'Javascript error when submitting display form';
        errorText.style.display = 'block';
    }
});