<template>
    <div class="contact-list pl-4 pr-2 pt-2" id="config-user">
        <div class="head-contact-list border-bottom border-light d-flex mt-3">
            <div class="figure mb-3" @click="open()"> 
                <img class="avatar avatar-head" :src="user.profile_img" :alt="user.name + '-foto'">
            </div>
            <div class="user-info">
                <h2 class="font-weight-bold h5">{{ user.name }}</h2>
                <p>{{user.phone_number}}</p>
            </div>
        </div>
        
        <div class="list-group">
            <h2>Settings</h2>
            <!-- Start of My Account -->
            <div class="category">
                
                <div class="data">
                    <h5 class="font-weight-bold text-black"><i class="fa fa-user mr-1"></i> Mi cuenta</h5>
                    <p>Actualiza los detalles de tu perfil</p>
                </div>
                <hr>
                <div class="content">

                    <!-- Image update container -->
                    <div class="upload mx-auto">
                        <div class="data mb-2">
                            <img class="avatar avatar-list mr-3" :src="avatar" alt="image">
                            <label>
                                <input class="d-none" type="file" @change="selectImg">
                                <span v-show="!isLoad" class="btn btn-primary btn-sm">Sube una imagen</span>
                            </label>

                            <button v-show="isLoad" @click="upload" class="btn btn-success btn-sm" :class="{'disabled':isLoading}" :disabled="isLoading">
                                {{ isLoading ? 'Loading..' : 'Save image' }}
                                <span v-if="isLoading" class="spinner-border spinner-border-sm ml-auto" role="status" aria-hidden="true">
                                </span>
                            </button>
                            <a v-show="isLoad" class="btn btn-danger btn-sm" href="#" @click="cancel"><i class="fas fa-times"></i></a>
                        </div>
                        <p>Para mejor resultado, usa una imagen de al menos 200px por 200px en formato .jpg o .png!</p>
                    </div>
                    <!-- END Image update container -->

                    <!-- Data update Form -->
                    <form class="mr-3 form position-relative">
                        <transition name="fade">
                            <div v-if="isLoadingUpdate" class="card-loader d-flex justify-content-center align-items-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                        </transition>
                        <div class="field" data-validate>
                            <label for="firstName">Nombre completo <span>*</span></label>
                            <input type="text" class="form-control inputs" @focus="hideValidate" name="name" v-model="data.name" required="">
                        </div>
                        <div class="field" data-validate>
                            <label for="email">Email <span>*</span></label>
                            <input type="text" class="form-control inputs" @focus="hideValidate" name="email" v-model="data.email" required="">
                        </div>
                        <div class="field" data-validate>
                            <label for="location">Numero de telefono</label>
                            <input type="number" class="form-control inputs" @focus="hideValidate"  name="phone" v-model="data.phone" required="">
                        </div>

                        <div class="buttons mt-3 d-flex">
                            <button class="btn btn-primary btn-sm w-50 mr-1" @click="update" :class="{'disabled': !isChanged}" :disabled="isLoadingUpdate">Guardar</button>
                            
                            <button class="btn btn-danger btn-sm w-50 disabled ml-1" disabled="true" title-placement="top" v-title="'Aun no disponible'">Borrar cuenta</button>
                        </div>
                        
                    </form>
                    <!-- END Data Update Form -->

                </div>
            </div>
            <!-- End of My Account -->
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: {
            type: Object,
            required: true
        },
        url:{
            type: String,
            required: true
        }
    },
    data() {
        return {
            avatar: this.user.profile_img,
            isLoad: false,
            file: false,
            data: {
                name: this.user.name,
                email: this.user.email,
                phone: this.user.phone_number,
                validate: false
            },
            isLoading: false, 
            isLoadingUpdate: false
        }
    },
    methods: {
        selectImg(e){
            let image = e.target.files[0];

            if (!image.type.match('image.*')) {
                this.$vToastify.error("no es un archivo valido bro");

                return
            }

            let reader = new FileReader();

            reader.readAsDataURL(image);
            reader.onload = e => {
                this.avatar = e.target.result
                this.isLoad = true
            }
            this.file = image
        },
        upload(){
            let data = new FormData;
            this.isLoading = true;

            data.append('image',this.file)
            if (!this.avatar.includes('default')) {
                data.append('option',true)
            }
            axios.post(`${this.url}/upload`,data).then( res => {
                this.isLoading = false
                if (res.data.status) {
                    this.$emit('updateImg',this.avatar)
                    this.$vToastify.success("Imagen actualizada");
                    this.cancel(true)
                }else{
                    this.$vToastify.error("nou");
                }
            })
        },
        cancel(op = false, e){
            if (!op) {
                e.preventDefault()
            }

            this.file = false
            this.avatar = this.user.profile_img
            this.isLoad = false
        },
        update(e){
            e.preventDefault()

            let form = document.getElementsByClassName('form'),
                data = new FormData(form[0]),
                inputs = document.getElementsByClassName('inputs'),
                validation = true

            const t = this

            _.forIn(inputs,(input,key) => {
                if (input.value == '') {
                    t.showValidate(input,"Este campo es requerido")
                }
            })

            if (!t.data.validate && t.isChanged) {
                t.isLoadingUpdate = true
                axios.post(`${this.url}/update`,data).then( (response) => {
                    t.isLoadingUpdate = false
                    if (response.data.status) {
                        this.$vToastify.success("Datos actualizados");
                        this.$emit('updateUser',response.data.user)
                    }else{
                        for (let index = 0; index < inputs.length; index++) {
                            const element = inputs[index];
                            _.forIn(response.data.errors, function(value, key) { 
                                if (key == element.attributes.name.value) {
                                    t.showValidate(element,value)
                                }
                            })
                        }
                        this.$vToastify.error("Oh no se pudo actualizar");
                    }

                })
            }


        },
        hideValidate(e){
            let element = e.target
            console.log(this.data.validate)
            if (this.data.validate) {
                this.data.validate = false
                element.parentElement.classList.remove('alert-validate')
            }
        },
        showValidate(e,value){
            e.parentElement.setAttribute('data-validate',value)
            e.parentElement.classList.add('alert-validate')
            this.data.validate = true
        }
    },
    computed: {
        isChanged(){
            let verify = false
            
            if (this.data.name != this.user.name ||
                this.data.email != this.user.email || 
                this.data.phone != this.user.phone_number) {
                
                verify = true
            }
            return verify
        }
    },
}
</script>
