<script>/**
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

import MenuEdit    from './MenuEdit';
import {Http, url} from '../http';


export default {
    components: {
        MenuEdit,
    },

    props: {
        // menus: {type: Array, default: () => []},
    },

    data: () => ({
        isEditing: false,
        editing  : null,
    }),

    methods: {
        menuCreate() {
            this.isEditing = true;
            this.editing   = {id: null, name: null, title: ''};
        },

        menuEdit(menuId, index) {
            console.log('menuEdit', menuId, index);
            this.editing   = this.$state.menus.slice(index, index + 1)[0];
            this.isEditing = true;
        },

        menuSave(menu) {
            console.log('menuSave', menu);

            if (!menu.name) {
                this.$store.addError('Введите имя меню');
                return;
            }

            if (!menu.title) {
                this.$store.addError('Введите название меню');
                return;
            }

            Http(url('menuSave'), menu)
                .then(res => {
                    if (menu.id) {
                        let idx = this.$state.menus.findIndex(m => m.id === menu.id);
                        this.$state.menus.splice(idx, 1, res.payload.menu);
                    } else {
                        this.$state.menus.push(res.payload.menu);
                    }

                    this.isEditing = false;
                    this.editing   = null;
                })
                .catch(this.$defaultError);
        },

        menuCancel() {
            this.isEditing = false;
            this.editing   = null;
        },

        menuDelete(menuId, index) {
            if (confirm('Меню будет удалено!')) {
                Http(url('menuDelete'), {id: menuId})
                    .then(res => {
                        this.$store.delMenuListItem(index);
                    })
                    .catch(this.$defaultError);
            }

        },

        showItems(menuId, index) {
            this.$store.showMenuItems(menuId, index);
        },
    },
};
</script>

<template>
    <div class="sectionBody">
        <div class="dynamic-tab-pane-control tab-pane" id="documentPane">
            <div class="tab-row-container">
                <div class="tab-row">
                    <h2 class="tab selected">
                        <span>Список меню</span>
                        <small>({{$state.menus.length}})</small>
                    </h2>
                </div>
            </div>
            <div class="tab-page">

                <div v-if="!isEditing">
                    <div class="form-group" v-if="$state.menus.length">
                        <div class="menu-list">
                            <div class="menu-list__row">
                                <div class="menu-list__cell _head _id">ID</div>
                                <div class="menu-list__cell _head _name">Имя</div>
                                <div class="menu-list__cell _head">Название</div>
                                <div class="menu-list__cell _head"></div>
                            </div>
                            <div class="menu-list__row"
                                v-for="(menu, index) in $state.menus" :key="menu.id"
                            >
                                <div class="menu-list__cell _id"><code>{{menu.id}}</code></div>
                                <div class="menu-list__cell _name">{{menu.name}}</div>
                                <div class="menu-list__cell">
                                    <button class="btn btn-link"
                                        :disabled="isEditing"
                                        @click.prevent="showItems(menu.id, index)"
                                    >{{menu.title}}
                                    </button>
                                </div>
                                <div class="menu-list__cell _actions">
                                    <div class="btn-group">
                                        <button class="btn btn-secondary btn-sm"
                                            :disabled="isEditing"
                                            @click.prevent="menuEdit(menu.id, index)"
                                        >Изменить
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm"
                                            :disabled="isEditing"
                                            @click.prevent="menuDelete(menu.id, index)"
                                        >Удалить меню
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning" v-else>Меню не создано</div>
                </div>

                <p v-if="!isEditing">
                    <button class="btn btn-primary" @click.prevent="menuCreate">Новое меню</button>
                </p>

                <MenuEdit v-else
                    :menu="editing"
                    @cancel="menuCancel"
                    @save="menuSave"
                ></MenuEdit>

            </div><!-- // .tab-page -->
        </div><!-- // .tab-pane -->
    </div>
</template>
