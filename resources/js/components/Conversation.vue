<template>
    <div class="conversation" id="conversation-component">
        <div class="d-flex head-conversation p-3">
            <div class="" v-if="contact">
                <img :src="contact.profile_img" class="rounded-circle avatar avatar-head" width="60" alt="">
            </div>
            <div class="info-contact ml-3">
                <p class="h5" :class="{'font-weight-light': !contact}">{{contact ? contact.name : 'Selecciona un contacto'}}</p>
                <p v-if="contact">{{contact.phone_number}}</p>
            </div>
        </div>
        <MessagesFeed @isLoad="changeLoad($event)" :sendedMessage="sendedMessage" :loadConver="loadConver" :messages="messages" :isRead="isRead" :contact="contact"></MessagesFeed>
        <SendMessage v-if="contact" @send="sendMessage($event)"></SendMessage>
    </div>
</template>

<script>
    
    import MessagesFeed from './MessageFeed'
    import SendMessage from './SendMessage'

    export default {
        props: {
            messages: {
                type: Array,
                default: []
            },
            contact: {
                default: false
            },
            isRead: {
                type: Boolean,
            },
            url:{
                type: String,
                required: true
            },
            loadConver: {
                type: Boolean
            }
        },
        data() {
            return {
                sendedMessage: false
            }
        },
        methods: {
            sendMessage(text) {
                let curDate = Date()
                this.sendedMessage = true
                axios.post(`${this.url}/send`,{
                        message: text,
                        date: curDate,
                        contactId: this.contact.id
                    }).then((response) => {
                        this.sendedMessage = false
                        this.$emit('newMessage',response.data)
                    })
            },
            changeLoad(e){
                this.$emit('isLoad',e)
            }
        },
        components: {
            MessagesFeed,
            SendMessage
        }
    }

</script>
