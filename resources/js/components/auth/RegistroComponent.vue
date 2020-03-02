<template>
    <div class="limiter auth-component">
    <div class="container-login100">
        <div class="wrap-login100">
            <div class="login100-pic js-tilt" data-tilt>
                <img class="mt-5" src="uploads/img-01.png" alt="IMG">
            </div>

            <form class="login100-form validate-form" data-option="registro">
                <span class="login100-form-title">
                    ChatApp Registro
                </span>

                <div class="wrap-input100 validate-input" data-validate = "Valid user is required">
                    <input @focus="hideValidate" @input="hideValidate" class="input100" type="text" v-model="data.name" name="name" placeholder="Usuario">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                    </span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                    <input @focus="hideValidate" @input="hideValidate"  class="input100" type="text" v-model="data.email" name="email" placeholder="Email">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                    </span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Valid number is required: 04241234567">
                    <input @focus="hideValidate" @input="hideValidate" class="input100" type="number" v-model="data.phone"  min="8" name="phone" placeholder="Phone number">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-phone" aria-hidden="true"></i>
                    </span>
                </div>

                <div class="wrap-input100 validate-input" data-validate = "Password is required">
                    <input @focus="hideValidate" @input="hideValidate" class="input100" type="password" v-model="data.password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                </div>
                
                <div class="wrap-input100 validate-input" data-validate = "Confirm password is required">
                    <input @focus="hideValidate" @input="hideValidate" class="input100" type="password" v-model="data.password_confir" name="password_confirmation" placeholder="Confirm password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </span>
                </div>
                
                <div class="container-login100-form-btn">
                     <button @click="validate" class="login100-form-btn btn-form" :class="{'disabled':isLoading}" :disabled="isLoading">
                        {{ isLoading ? 'Loading..' : 'Registrarme' }}
                        <span v-if="isLoading" class="spinner-border spinner-border-sm ml-auto" role="status" aria-hidden="true">
                        </span>
                    </button>
                </div>

                <div class="text-center p-t-136">
                    <a class="txt2" href="/">
                        Iniciar sesion
                        <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            data:{
                email: '',
                password: '',
                password_confir: '',
                name: '',
                phone: '',
                validate: false 
            },
            isLoading: false
        }
    },
    methods: {
        validate(e){
            e.preventDefault()

            console.log('validatee')
            let inputs = document.getElementsByClassName('input100'),
                form = document.getElementsByClassName('validate-form'),
                data = ''

            const t = this
            
            for (let index = 0; index < inputs.length; index++) {
                const element = inputs[index];

                if (element.value == '') {
                    t.showValidate(element,"Este campo es requerido")
                }else if (element.attributes.name.value == 'email' && !this.validEmail(element.value)) {
                    t.showValidate(element,"El email no es valido")
                }else if (element.attributes.name.value == 'phone' && element.value.length <8) {
                    t.showValidate(element,"El telefono no es valido")
                }
            }

            if (!t.data.validate) {
                data = new FormData(form[0])

                axios.post(`/register`,data).then( (response) => {
                    if (response.data.status) {
                        window.location.assign('/home')
                    }else{
                        for (let index = 0; index < inputs.length; index++) {
                            const element = inputs[index];
                            _.forIn(response.data.errors, function(value, key) { 
                                if (key == element.attributes.name.value) {
                                    t.showValidate(element,value)
                                }
                            })
                        }
                        this.$vToastify.error("Hay un error en los datos");
                    }
                })
            }
        },
        validEmail: function (email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        },
        hideValidate(e){
            let element = e.target,
                ifValidate = ''

            if (this.data.validate) {
                element.parentElement.classList.remove('alert-validate')

                ifValidate = document.getElementsByClassName('alert-validate')

                if (ifValidate.length == 0) {
                    this.data.validate = false
                }
            }
        },
        showValidate(e,value,op = false){
            if (op) {
                for (let index = 0; index < e.length; index++) {
                    const element = e[index];

                    element.parentElement.setAttribute('data-validate',value)
                    element.parentElement.classList.add('alert-validate')
                }
            }else{
                e.parentElement.setAttribute('data-validate',value)
                e.parentElement.classList.add('alert-validate')
            }
            this.data.validate = true
        }
    }
}
</script>