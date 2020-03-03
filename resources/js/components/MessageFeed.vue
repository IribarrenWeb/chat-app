<template>
    <div class="feed p-1" id="feed-component" :class="{'overflow-hidden':isLoad}"  ref="feed">
        <div v-if="!contact" class="h-100 w-100 prev">
            <i class="fas fa-users"></i>
            <h3 class="h5 w-50">Selecciona a un contacto para comenzar a chatear.</h3>
        </div>
        <div v-else-if="contact && messages.length == 0" class="h-100 w-100 prev">
            <i class="far fa-comment-alt"></i>
            <h3 class="h5 w-50">Parece que no has comenzado a chatear con este usuario, enviale un mensaje!</h3>
        </div>
        <div v-else class="px-1" v-for="message in checkMessage" :key="message.id">
            <div class="date" v-if="message.date">
                <hr>
                    <span>{{formatTime(message.created_at,true)}}</span>
                <hr>
            </div>
            <div class="message position-relative" :class="{'send': message.to == contact.id,'mb-0':!message.hour}">

                <img v-if="message.ifLast" class="avatar avatar-feed mr-2" :src="message.from_contact.profile_img" 
                    title-placement="top" 
                    alt="avatar" 
                    v-title="message.from_contact.name">

                <div class="text-main" :class="{'spaceImage': !message.ifLast}" title-placement="right" v-title="formatTime(message.created_at,true) + ' ' +  formatTime(message.created_at)" >
                    <div class="text-group" :class="{'send': message.to == contact.id}">
                        <div class="text" :class="{'send': message.to == contact.id}">{{message.message}}</div>
                    </div>
                    <p v-if="message.hour" class="">{{formatTime(message.created_at)}}</p>
                </div>
                <div class="position-absolute">
                    <i v-if="message.ifCheck" class="fas" :class="message.ifCheck && message.read ? 'fa-check-double' : 'fa-check'"></i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        messages: {
            type: Array,
            default: []
        },
        contact: {
            type: Object,
            default: false
        },
        isRead: {
            type: Boolean,
        }
    },
    data() {
        return {
            isLoad: {
                type: Boolean
            },
        }
    },
    beforeMount(){
        this.isLoad = true
    },
    mounted() {
        this.isLoad = false
    },
    methods: {
        scrollDown(){
            setTimeout(() => {
                this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight; 
            },50)
        },
        formatTime(date,option = false){

            let d = new Date(date),
                td = new Date(),
                today = td.getDate() + '/' +  td.getMonth(),
                fullDate = d.getDate() + '/' +  d.getMonth(),
                h=d.getHours(),m=d.getMinutes(),l="AM";
            // console.log(d,date)
            if(option){
                let result = fullDate;

                if(today == fullDate){
                    result = 'Hoy';
                }else if((today - 1) == fullDate){
                    result = 'Ayer';
                }
                return result;
            }

            if(h > 12){
                h = h - 12;
            }
            if(h < 10){
                h = '0'+h;
            }
            if(m < 10){
                m = '0'+m;
            }
            if(d.getHours() >= 12){
                l="PM"
            }else{
                l="AM"
            }

            return h+':'+m+' '+l;

        }
    },
    watch: {
        messages(){
            this.scrollDown();
        },
        contact(){
 
            this.scrollDown();
        }
    },
    computed: {
        checkMessage(){
            for (let index = 0; index <= this.messages.length - 1; index++) {
                
                /** Init the variables to use it */
                const message = this.messages[index],
                    totalMessages = this.messages.length -1    
                let compareMessage,
                    prevMessage = false,
                    nextMessage = false

                
                if (this.messages[index + 1]) {
                    nextMessage = this.messages[index + 1]
                }else{
                    if(this.messages.length >= 2){
                       prevMessage = this.messages[index - 1];
                    }
                }
                
                /** Check the message date */
                message.date = true

                if(index >= 1){

                    if (nextMessage) {
                        compareMessage = nextMessage
                    } else if(prevMessage) {
                        compareMessage = prevMessage
                    }else{
                        compareMessage = false
                    }

                    // console.log(prevMessage)

                    if (compareMessage) {
                        if(this.formatTime(message.created_at,true) == this.formatTime(compareMessage.created_at,true)){
                            message.date = false
                        }
                    }
                }

                /** Check if is the last message from sender */

                if (message.from == this.contact.id) {
                
                    message.ifLast = true

                    if (nextMessage.from == message.from) {
                        message.ifLast = false
                    }
                }

                /** Check the read icon */
                
                if (totalMessages === index) {
                    if (message.from != this.contact.id) {
                        if(prevMessage && prevMessage.from != this.contact.id){
                            prevMessage.ifCheck = false
                        }
                        message.ifCheck= true
                    }else{
                        if(prevMessage && prevMessage.from != this.contact.id){
                            prevMessage.ifCheck = false
                        }               
                    }

                }
                
            }

            return this.messages
        }
    }
}
</script>