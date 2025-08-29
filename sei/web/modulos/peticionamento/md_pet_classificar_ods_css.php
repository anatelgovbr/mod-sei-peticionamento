/**
 * ANATEL
 *
 * 29/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */
 
h1 {
text-align: center;
}
h2 {
margin: 0;
}
#multi-step-form-container {
margin-top: 5rem;
}
.text-center {
text-align: center;
}
.mx-auto {
margin-left: auto;
margin-right: auto;
}
.pl-0 {
padding-left: 0;
}
.button {
padding: 0.7rem 1.5rem;
border: 1px solid #4361ee;
background-color: #4361ee;
color: #fff;
border-radius: 5px;
cursor: pointer;
}
.submit-btn {
border: 1px solid #0e9594;
background-color: #0e9594;
}
.mt-3 {
margin-top: 2rem;
}
.d-none {
display: none;
}
.form-step {
border: 1px solid rgba(0, 0, 0, 0.1);
border-radius: 20px;
padding: 3rem;
}
.font-normal {
font-weight: normal;
}
ul.form-stepper {
counter-reset: section;
margin-bottom: 3rem;
}
ul.form-stepper .form-stepper-circle {
position: relative;
}
ul.form-stepper .form-stepper-circle span {
position: absolute;
top: 50%;
left: 50%;
transform: translateY(-50%) translateX(-50%);
}
.form-stepper-horizontal {
position: relative;
display: -webkit-box;
display: -ms-flexbox;
display: flex;
-webkit-box-pack: justify;
-ms-flex-pack: justify;
justify-content: space-between;
}
ul.form-stepper > li:not(:last-of-type) {
margin-bottom: 0.625rem;
-webkit-transition: margin-bottom 0.4s;
-o-transition: margin-bottom 0.4s;
transition: margin-bottom 0.4s;
}
.form-stepper-horizontal > li:not(:last-of-type) {
margin-bottom: 0 !important;
}
.form-stepper-horizontal li {
position: relative;
display: -webkit-box;
display: -ms-flexbox;
display: flex;
-webkit-box-flex: 1;
-ms-flex: 1;
flex: 1;
-webkit-box-align: start;
-ms-flex-align: start;
align-items: start;
-webkit-transition: 0.5s;
transition: 0.5s;
}
.form-stepper-horizontal li:not(:last-child):after {
position: relative;
-webkit-box-flex: 1;
-ms-flex: 1;
flex: 1;
height: 1px;
content: "";
top: 32%;
}
.form-stepper-horizontal li:after {
background-color: #dee2e6;
}
.form-stepper-horizontal li.form-stepper-completed:after {
background-color: #4da3ff;
}
.form-stepper-horizontal li:last-child {
flex: unset;
}
ul.form-stepper li a .form-stepper-circle {
display: inline-block;
width: 40px;
height: 40px;
margin-right: 0;
line-height: 1.7rem;
text-align: center;
background: rgba(0, 0, 0, 0.38);
border-radius: 50%;
}
.form-stepper .form-stepper-active .form-stepper-circle {
background-color: #2196F3 !important;
color: #fff;
}
.form-stepper .form-stepper-active .label {
color: #2196F3 !important;
}
.form-stepper .form-stepper-active .form-stepper-circle:hover {
background-color: #2196F3 !important;
color: #fff !important;
}
.form-stepper .form-stepper-unfinished .form-stepper-circle {
background-color: #f8f7ff;
}
.form-stepper .form-stepper-completed .form-stepper-circle {
background-color: #0e9594 !important;
color: #fff;
}
.form-stepper .form-stepper-completed .label {
color: #0e9594 !important;
}
.form-stepper .form-stepper-completed .form-stepper-circle:hover {
background-color: #0e9594 !important;
color: #fff !important;
}
.form-stepper .form-stepper-active span.text-muted {
color: #fff !important;
}
.form-stepper .form-stepper-completed span.text-muted {
color: #fff !important;
}
.form-stepper .label {
font-size: 1rem;
margin-top: 0.5rem;
}

.form-stepper a {
cursor: default;
}

div.toggle-btn {
    position:absolute;
    top:50%;
    left:50%;
    transform:tranlate(-50%,-50%);
}

/* The switch - the box around the slider */
.switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 25px;
    margin-right: 10px
}

/* Hide default HTML checkbox */
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

/* The slider */
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 19px;
    width: 19px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider.round:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
    border-radius: 17px;
}

.slider.round:before {
    border-radius: 50%;
}

#objetivosOds img.desfoque, #objetivosOds img.sugestaoIa, #objetivosOds img.historico {
    opacity: 0.25;
}

.colorido {
    opacity: 100 !important;
}

@media (max-width: 576px) {
    #objetivosOds .imagem {
        width: 95%;
        height: auto;
    }
    div.imagem > div.historico, div.imagem > div.sugestaoIa {
        left: 25px;
        bottom: 15px;
    }
}

.img-desfoque {
    opacity: 0.25;
}

li.form-stepper-list {
width: 150px;
cursor: pointer !important;
}

li.form-stepper-list a {
cursor: pointer !important;
}

button {
cursor: pointer !important;
}

.item_meta_fraca {
 display: none;
}
