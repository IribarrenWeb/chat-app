<template>
    <div id="contact-list" class="contact-list px-4 pt-2">
        <div class="head-contact-list d-flex mt-3">
            <div class="figure" @click="open()"> 
                <img class="avatar" :src="user.profile_img" :alt="user.name + '-foto'">
            </div>
            <div class="user-info">
                <h2 class="font-weight-bolder h5">{{ user.name }}</h2>
                <p>{{user.phone_number}}</p>
            </div>
            <div class="navi ml-4 h-auto">
                <a href="#nav">
                    <i class="fas fa-cog pointer-event" @click="open()"></i>
                </a>
            </div>
        </div>

        <div class="search">
            <div class="form-inline position-relative">
                <input type="search" v-model="filterText" class="form-control" id="conversations" placeholder="Busca algun contacto...">
                <button type="button" class="btn btn-link loop"><i class="fa fa-search"></i></button>
            </div>
            <button class="btn create" data-toggle="modal" data-target="#startnewchat"><i class="ti-pencil"></i></button>
        </div>
        
        <div class="list-group">
            <div v-for="contact in orderContacts" :key="contact.id" 
                class=" d-flex align-items-center border-right-0"
                :class="{ 'selected': selected == contact}"
                @click="selectingContact(contact)">
                <img :src="contact.profile_img" class="rounded-circle avatar avatar-list" alt="">
                <div class="info-contact ml-2">
                    <p class="font-weight-bold name text-capitalize">{{contact.name}}</p>
                    <p>{{contact.phone_number}}</p>
                </div>
                <span v-if="contact.unread > 0" class="badge badge-pill badge-success">{{contact.unread}}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            contacts:{
                type:Array,
                default:[]
            },
            user: {
                // type: Object,
                required: true
            }
        },
        data() {
            return {
                selected: 0,
                buttons: false,
                filterText: ''
            }
        },
        methods: {
            selectingContact(contact){
                if(this.selected == contact){
                    return
                }
                this.selected = contact

                this.$emit('selected',contact)
            },
            open(){
                // console.log('click')
                this.buttons = !this.buttons
                this.$emit('change',this.buttons)
            }
        },
        computed: {
            orderContacts(){
                
                let sort = ''
                if (this.filterText.length >= 1) {
                    let filter = new RegExp(this.filterText, 'i')
                    sort = this.contacts.filter(e => e.name.match(filter))
                } else {
                    sort = _.sortBy(this.contacts,[(contact) => {
                        if(this.selected == contact){
                            return Infinity;
                        }

                        return contact.unread;
                    }]).reverse()    
                }
                
                return sort

            }
        }
    }
</script>


