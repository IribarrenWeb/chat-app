<template>
    <div class="limiter auth-component">
      <div class="container-login100">
        <div class="wrap-login100">
          <div class="login100-pic js-tilt" data-tilt>
            <img src="uploads/img-01.png" alt="IMG" />
          </div>

          <form class="login100-form validate-form">
            
            <div class="login100-form-title">
                <img class="p-2 rounded-circle shadow-sm bg-light" src="uploads/chatapp.jpg" width="100" alt="brand">
                <span class="d-block mt-1">Login</span>
            </div>

            <div
              class="wrap-input100 validate-input"
              data-validate="Valid email is required: ex@abc.xyz"
            >
                <input class="input100" @focus="hideValidate" type="email" v-model="data.email" name="email" placeholder="Email" />
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
            </div>

            <div class="wrap-input100 validate-input" data-validate="Password is required">
                <input class="input100" @focus="hideValidate" type="password" v-model="data.password" name="password" placeholder="Contraseña" />
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
            </div>

            <div class="container-login100-form-btn">
                <button @click="validate" class="login100-form-btn" :class="{'disabled':isLoading}" :disabled="isLoading">
                    {{ isLoading ? 'Loading..' : 'Login' }}
                    <span v-if="isLoading" class="spinner-border spinner-border-sm ml-auto" role="status" aria-hidden="true">
                    </span>
                </button>
            </div>

            <div class="text-center p-t-136">
                <a class="txt2" href="/registro">
                    Crea tu cuenta
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
                validate: false,
            },
            isLoading: false
        }
    },
    beforeMount() {
    },
    methods: {
        validate(e){
            e.preventDefault()
            console.log('validatee')
            let inputs = document.getElementsByClassName('input100'),
                form = document.getElementsByClassName('validate-form'),
                data = new FormData(form[0])

            const t = this

            _.forIn(inputs,(input,key) => {
                if (input.value == '') {
                    console.log('vaciooo')

                    t.showValidate(input,"Este campo es requerido")
                }
            })

            if (!t.data.validate) {
                t.isLoading = true
                axios.post(`/login`,data).then( (response) => {
                    t.isLoading = false
                    if (response.data.status) {
                        window.location.assign('/home')
                    }else{
                        t.showValidate(inputs,"Datos invalidos",true)
                    }
                })
            }
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
};
</script>