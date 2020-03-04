<template>
    <div class="text my-2">
        <div class="form-group mb-0 px-3">
          <textarea class="form-control" @keypress.enter.prevent="sendMessage(text)" v-model="text"></textarea>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            text: '',
        }
    },
    methods: {
        sendMessage(text){
            if(text == ''){
                return
            }
            
            this.$emit('send',text)
            this.text = ''
        }
    }
}
</script>