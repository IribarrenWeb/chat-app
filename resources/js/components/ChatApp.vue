<template>
    <div class="chat-app d-flex">
        <NavigationMenu :url="url" :option="option" @option="changeComponent" :isActive="isActive"></NavigationMenu>

        <transition name="fade" mode="out-in" appear>
            <keep-alive>
                <ContactList v-if="option == false" :user="user" :contacts="contacts" @change="showNavi($event)"  @selected="startChat($event)"></ContactList>
                <ConfigUser :url="url" @updateUser="user = $event" @updateImg="user.profile_img = $event" v-else :user="user"></ConfigUser>
            </keep-alive>
        </transition>

        <Conversation :url="url" :contact="selectedContact" :isRead="isRead" :messages="messages" @newMessage="pushNewMessage"></Conversation>
    </div>
</template>

<script>
    import Conversation from './Conversation'
    import ContactList from './ContactList'
    import NavigationMenu from './NavigationMenu'
    import ConfigUser from './ConfigUser'
    import VTitle from 'v-title';
    
    export default {
        data() {
            return {
                selectedContact: null,
                contacts: [],
                messages: [],
                isRead: false,
                url: '',
                audio: new Audio('http://chatapp.kiribarren.top/uploads/messenger.mp3'),
                isActive: false,
                option: false,
                user: ''
            }
        },
       async mounted() {
            console.log('mounted')

            await this.getUser()

            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (e) => {
                    this.handleEvent(e.message)
                })
                .listen('ReadMessages', (e) => {
                    this.handleReadEvent(e.contactId)
                })
            
            axios.get('/contacts')
                .then((response) => {
                    this.contacts = response.data
                })
        },
        components: {
            Conversation,
            ContactList,
            NavigationMenu,
            ConfigUser
        },
        methods: {
            async startChat(contact){
                
                this.updateReadMessage(contact,true)

                await axios.get(`${this.url}/conversation/${contact.id}`)
                    .then((response) => {
                        this.messages = response.data
                        this.selectedContact = contact
                    })
                
            },
            async getUser(){
                await axios.get(`${this.url}/auth`)
                .then( res => {
                    this.user = res.data.user
                }) 
            },
            pushNewMessage(message){
                this.messages.push(message)
                if(message.from != this.user.id){
                    this.audio.play()
                }
            },
            handleEvent(message){
                if (this.selectedContact != null) {
                    if(this.selectedContact.id == message.from){
                        this.pushNewMessage(message)
                        axios.post(`${this.url}/read`,{contactId: this.selectedContact.id})
                        return
                    }
                }
                
                this.updateReadMessage(message.from_contact)
                
            },
            handleReadEvent(contactId){
                if (this.selectedContact != null) {
                    if(this.user.id == contactId){
                        this.messages.map( (message) => {
                            if(message.from == this.user.id){
                                return message.read = true
                            }
                            return message
                        })
                    }
                }
            },
            updateReadMessage(contact,optionReset = false){
                this.contacts.map( (item) => {

                    if(item.id != contact.id){
                        return item
                    }

                    if(optionReset)
                        return item.unread = 0
                    else
                        this.audio.play()
                        return item.unread += 1
                })
            },
            showNavi(event){
                // console.log('active',event)
                this.isActive = event
            },
            changeComponent(event){
                this.option = event
            }
        }
    }
</script>

