/**
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

let timerId;

export const store = {
    state: {
        err : [],
        view: null,

        menus: [],

        editingMenu: null,
        // editingMenuItems: null,
    },

    addError(err) {
        clearTimeout(timerId);
        this.state.err.push(err);
        timerId = setTimeout(() => this.clearErrors(), 10000);
    },

    clearErrors() {
        this.state.err = [];
    },

    setView(view) {
        this.state.view = view;
    },

    showMenuList() {
        this.setView('MenuList');
        this.state.editingMenu = null;
    },

    showMenuItems(menuId, index) {
        this.state.editingMenu = this.state.menus.slice(index, index + 1)[0];
        this.setView('ItemList');
    },

    setMenuList(menuList) {
        this.state.menus = menuList;
    },

    delMenuListItem(index) {
        return this.state.menus.splice(index, 1);
    },

    getMenu(menuId) {
        let idx  = this.state.menus.findIndex(m => m.id === menuId);
        let menu = this.state.menus.slice(idx, idx + 1);
        if (menu.length) return menu[0];
        return null;
    },
};

export const Store = {
    install(Vue) {
        Object.defineProperty(Vue.prototype, '$store', {
            get() { return this.$root.$data.store; },
        });
        Object.defineProperty(Vue.prototype, '$state', {
            get() { return this.$root.$data.store.state; },
        });
    },
};
