<template>
    <div class="feed p-1" id="feed-component" ref="feed">
        <transition-group name="fade">
            
            <!-- Loader for message feed -->
            <div v-if="!loadConver" key="load" class="d-flex bg-light-trans h-100 justify-content-center align-items-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <!-- Message feed container -->
            <div key="conver" class="h-100" v-if="loadConver">

                <!-- Conditional texts -->
                <div v-if="!contact" class="h-100 w-100 prev">
                    <i class="fas fa-users"></i>
                    <h3 class="h5 w-50">Selecciona a un contacto para comenzar a chatear.</h3>
                </div>
                <div v-else-if="contact && messages.length == 0" class="h-100 w-100 prev">
                    <i class="far fa-comment-alt"></i>
                    <h3 class="h5 w-50">Parece que no has comenzado a chatear con este usuario, enviale un mensaje!</h3>
                </div><!-- End Conditionals text -->

                <!-- Message container -->
                <div v-else class="px-1" v-for="message in checkMessage" :key="message.id">
                    <!-- Message date if is set -->
                    <div class="date" v-if="message.date">
                        <hr>
                            <span>{{formatTime(message.created_at,true)}}</span>
                        <hr>
                    </div>
                    <!-- Message div -->
                    <div class="message position-relative" :class="{'send': message.to == contact.id,'mb-0':!message.hour}">

                        <!-- Image only for message from contact -->
                        <img v-if="message.ifLast" class="avatar avatar-feed mr-2" :src="message.from_contact.profile_img" 
                            title-placement="top" 
                            alt="avatar" 
                            v-title="message.from_contact.name">

                        <!-- Message text -->
                        <div class="text-main" :class="{'spaceImage': !message.ifLast}" title-placement="right" v-title="formatTime(message.created_at,true) + ' ' +  formatTime(message.created_at)" >
                            <div class="text-group" :class="{'send': message.to == contact.id}">
                                <div class="text" :class="{'send': message.to == contact.id}">{{message.message}}</div>
                            </div>
                            <p v-if="message.hour" class="">{{formatTime(message.created_at)}}</p>
                        </div>

                        <!-- Message check -->
                        <div class="position-absolute">
                            <i v-if="message.ifCheck" class="fas" :class="message.ifCheck && message.read ? 'fa-check-double' : 'fa-check'"></i>
                        </div>

                    </div> <!-- End Message div -->

                </div> <!-- End Message container -->
                
                <!-- Notify sended message -->
                <div v-show="sendedMessage" class="message send">
                    <!-- Message text -->
                    <div class="text-main">
                        <div class="text-group send">
                            <div class="text send bg-primary text-white">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Enviando...
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End Message feed container -->

        </transition-group>
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
        },
        loadConver: {
            type: Boolean
        },
        sendedMessage: {
            type: Boolean
        }
    },
    data() {
        return {
        }
    },
    mounted() {
        this.$emit('isLoad',true);
    },
    methods: {
        scrollDown(){
            setTimeout(() => {
                this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight; 
            },50)
        },
        formatTime(date,option = false){
            const month = new Array();
            month[0] = "Ene";
            month[1] = "Feb";
            month[2] = "Mar";
            month[3] = "Abril";
            month[4] = "Mayo";
            month[5] = "Jun";
            month[6] = "Jul";
            month[7] = "Ago";
            month[8] = "Sep";
            month[9] = "Oct";
            month[10] = "Nov";
            month[11] = "Dic";

            let d = new Date(date),
                td = new Date(),
                tdYear = td.getFullYear(),
                dYear = d.getFullYear(),
                today = td.getDate() + ' ' +  month[td.getMonth()],
                beforeDay = (td.getDate() - 1) + ' ' +  month[td.getMonth()],
                fullDate = d.getDate() + ' ' +  month[d.getMonth()],
                h=d.getHours(),m=d.getMinutes(),l="AM";

            if(option){
                let result = fullDate;

                if((today + ' ' + tdYear) == (fullDate + ' ' + dYear)){
                    result = 'Hoy';
                }else if((beforeDay + ' ' + tdYear) == (fullDate + ' ' + dYear)){
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
        contact(){
            this.scrollDown();
        },
        sendedMessage(){
            console.log('scroll')
            this.scrollDown();
        }
    },
    computed: {
        checkMessage(){
            this.scrollDown();

            for (let index = 0; index <= this.messages.length - 1; index++) {
                
                /** Init the variables to use it */
                const message = this.messages[index],
                    totalMessages = this.messages.length -1    
                let compareMessage,
                    prevMessage = false,
                    nextMessage = false

                
                if (this.messages.length >= 2) {
                    if(typeof this.messages[index + 1] == 'undefined' && index >= 2){
                        prevMessage = this.messages[index - 1];
                    }else{
                        prevMessage = this.messages[index - 1];
                        nextMessage = this.messages[index + 1]
                    }
                }
                
                /** Check the message date */
                message.date = true

                if(index > 0){
                    if(prevMessage) {
                        if(this.formatTime(message.created_at,true) == this.formatTime(prevMessage.created_at,true)){
                            message.date = false
                        }
                    }
                }

                /** Check if is the last message from sender */

                if (message.from == this.contact.id) {
                
                    message.ifLast = true

                    if (nextMessage.from == message.from && nextMessage) {
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