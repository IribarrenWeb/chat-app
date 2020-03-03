<template>
    <div class="navigation" id="navigation-component" :class="{'open': isActive == true}">
        <div class="container px-0 overflow-hidden">
            <div class="inside">
                <div class="nav nav-tab menu">
                    <a href="#" @click="changeEvent()" data-toggle="tab" title="User Setting">
                        <i class="fas fa-sliders-h"></i>
                        Configuracion
                    </a>
                    <!-- <a href="#members" data-toggle="tab" title="All members">
                        <i class="fas fa-home"></i>
                        All Friends
                    </a>
                    <a href="#discussions" data-toggle="tab" class="active" title="Chat">
                        <i class="fas fa-users"></i>
                        Recent Chat
                    </a> -->
                    <a :href="`${this.url}/logout`" class="btn power border-0" title="Sign Out">
                        <i class="fas fa-power-off"></i>
                        Salir
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isActive: {
            type: Boolean,
            required: true
        },
        option: {
            type: Boolean,
            required: true,
        },
        url:{
            type: String,
            required: true
        }
    },
    data() {
        return {
            optionEvent: this.option
        }
    },
    methods: {
        changeEvent(){
            this.optionEvent = !this.optionEvent

            this.$emit('option',this.optionEvent)
        }
    }
}
</script>
