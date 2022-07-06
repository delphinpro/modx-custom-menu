/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

import './styles/index.scss';
import Vue from 'vue';
import Http, { url } from './http';
import { store, Store } from './store';
import MenuList from './components/MenuList';
import ItemList from './components/ItemList';
import TreeNode from './components/TreeNode';
import DocSelector from './components/DocSelector';
import DocSelectorNode from './components/DocSelectorNode';


console.clear();

Vue.use(Store);
Vue.use({
    install(Vue) {
        Object.defineProperty(Vue.prototype, '$defaultError', {
            get() {
                return (err) => {
                    if (err.response) {
                        store.addError(err.response.message);
                    } else {
                        store.addError(err.message);
                        console.error(err);
                    }
                };
            },
        });
    },
});

Vue.component('TreeNode', TreeNode);
Vue.component('DocSelectorNode', DocSelectorNode);

const themeModes = ['', 'lightness', 'light', 'dark', 'darkness'];
if (themeModes[parseInt(localStorage['MODX_themeMode'])]) {
    document.body.classList.remove('lightness');
    document.body.classList.remove('light');
    document.body.classList.remove('dark');
    document.body.classList.remove('darkness');
    document.body.classList.add(themeModes[parseInt(localStorage['MODX_themeMode'])]);
}

new Vue({
    el: '#app',

    components: {
        MenuList,
        ItemList,
        DocSelector,
    },

    data: {
        store,
        docSelectorShow: true,
    },

    created() {
        console.log('App created');

        Http(url('menuList')).then(res => {
            this.$store.setMenuList(res.payload.menuList);
            this.$store.showMenuList();
        }).catch(this.$defaultError);
    },

    mounted() {
        this.$nextTick(() => this.$el.classList.add('AppMounted'));
    },

    methods: {},
});
