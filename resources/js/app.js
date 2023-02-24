import './bootstrap';
import Search from "./live-search"
// alert('THis is a test 1234');
import Chat from "./chat";
import Profile from "./profile";
if(document.querySelector(".header-search-icon")){
    new Search();
}

if(document.querySelector(".header-chat-icon")){
    new Chat();
}

if(document.querySelector(".profile-nav")){
    new Profile();
}

