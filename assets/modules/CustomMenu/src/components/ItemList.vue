<script>/**
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

import {Http, url} from '../http';
import ItemEdit    from './ItemEdit';


export default {
    components: {
        ItemEdit,
    },

    props: {
        menu: {type: Object, default: null},
    },

    data: () => ({
        isEditing  : false,
        editingItem: null,
        items      : [],
    }),

    created() {
        this.itemList(this.menu.id);
    },

    methods: {
        backToMenuList() {
            this.$store.setView('MenuList');
        },

        itemList(menuId) {
            this.editingItem = null;
            this.isEditing   = false;
            Http(url('itemList'), {menuId: menuId}).then(res => {
                this.items = res.payload.itemsTree;
            }).catch(this.$defaultError);
        },

        itemCreate() {
            this.editingItem = null;
            this.isEditing   = true;
        },

        itemCancel() {
            this.isEditing = false;
        },

        itemEdit(item) {
            this.editingItem = item;
            this.isEditing   = true;
        },

        itemOrder(item, dir) {
            Http(url('itemOrder'), {
                id      : item.id,
                parentId: item.parentId,
                menuId  : item.menuId,
                dir     : dir,
            }).then(res => {
                this.itemList(item.menuId);
            }).catch(this.$defaultError);
        },

        itemDelete(item) {
            if (confirm('Пункт меню будет удалён со всеми подпунктами!')) {
                Http(url('itemDelete'), {id: item.id}).then(res => {
                    this.itemList(item.menuId);
                }).catch(this.$defaultError);
            }
        },

        onItemSave(item) {
            this.itemList(item.menuId);
        },
    },
};
</script>

<template>
    <div>
        <div class="sectionBody">
            <div class="dynamic-tab-pane-control tab-pane" id="documentPane">
                <div class="tab-row-container">
                    <div class="tab-row">
                        <button class="tab" @click.prevent="backToMenuList" :disabled="isEditing">
                            <span>Список меню</span>
                            <small>({{$state.menus.length}})</small>
                        </button>
                        <h2 class="tab selected">
                            <span>Редактировать пункты</span>
                        </h2>
                    </div>
                </div>
                <div class="tab-page">
                    <h4 style="margin-bottom: 1rem;">
                        <span>{{menu.title}}</span>
                        <small>({{menu.name}}, {{menu.id}})</small>
                    </h4>

                    <div class="form-group" v-if="!isEditing">
                        <div class="treeRoot" v-if="items.length">
                            <TreeNode v-for="(item, index) in items" :key="item.id"
                                :node="item"
                                :index="index"
                                :parent-len="items.length"
                                :disabled="isEditing"
                                @change="itemEdit"
                                @order="itemOrder"
                                @delete="itemDelete"
                            ></TreeNode>
                        </div>
                        <div class="alert alert-warning" v-else>Пункты не созданы</div>
                    </div>

                    <div v-if="!isEditing">
                        <div class="form-group">
                            <button class="btn btn-primary" @click.prevent="itemCreate">Добавить пункт</button>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-secondary" @click.prevent="backToMenuList">
                                Завершить редактирование
                            </button>
                        </div>
                    </div>

                    <ItemEdit v-else
                        :menu="menu"
                        :menu-item="editingItem"
                        :menu-items="items"
                        @cancel="itemCancel"
                        @itemSave="onItemSave"
                    ></ItemEdit>

                </div><!-- // .tab-page -->
            </div><!-- // .tab-pane -->
        </div>

        <!--<div id="treeRoot">-->
        <!--<div id="node2" draggable="true">-->
        <!--<a class="node" data-published="1" data-deleted="0" data-isfolder="1" data-level="0">-->
        <!--<span class="indent"></span>-->
        <!--<span class="toggle"><i class="fa fa-angle-down"></i></span>-->
        <!--<span class="icon"><i class="fa fa-folder-open"></i></span>-->
        <!--<span class="title">Blog</span><small>(2)</small>-->
        <!--</a>-->
        <!--<div>-->
        <!--<div id="node9">-->
        <!--<a class="node hidemenu selected" data-published="1" data-deleted="0" data-isfolder="0"-->
        <!--data-level="1">-->
        <!--<span class="indent"><i></i></span>-->
        <!--<span class="icon"><i class="fa fa-file-o"></i></span>-->
        <!--<span class="title">Mini-Blog HOWTO</span><small>(9)</small>-->
        <!--</a>-->
        <!--</div>-->
        <!--<div id="node18">-->
        <!--<a class="node hidemenu" data-published="1" data-deleted="0" data-isfolder="0" data-level="1">-->
        <!--<span class="indent"><i></i></span>-->
        <!--<span class="icon"><i class="fa fa-file-o"></i></span>-->
        <!--<span class="title">Just a pretend, older post</span><small>(18)</small>-->
        <!--</a>-->
        <!--</div>-->
        <!--<div id="node47">-->
        <!--<a class="node hidemenu" data-published="1" data-deleted="1" data-isfolder="0" data-level="1">-->
        <!--<span class="indent"><i></i></span>-->
        <!--<span class="icon"><i class="fa fa-file-o"></i></span>-->
        <!--<span class="title">New documentation site: docs.evo.im</span><small>(47)</small>-->
        <!--</a>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
    </div>
</template>
