<!--
  Evo Custom Menu
  Copyright (c) 2018-2022
  delphinpro <delphinpro@yandex.ru>
  -->

<script>
import { Http, url } from '../http';
import ItemEdit from './ItemEdit';


export default {
    components: {
        ItemEdit,
    },

    props: {
        menu: { type: Object, default: null },
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
            this.isEditing = false;
            Http(url('itemList'), { menuId: menuId }).then(res => {
                this.items = res.payload.itemsTree;
            }).catch(this.$defaultError);
        },

        itemCreate() {
            this.editingItem = null;
            this.isEditing = true;
        },

        itemCancel() {
            this.isEditing = false;
        },

        itemEdit(item) {
            this.editingItem = item;
            this.isEditing = true;
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
                Http(url('itemDelete'), { id: item.id }).then(res => {
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
            <div id="documentPane" class="dynamic-tab-pane-control tab-pane">
                <div class="tab-row-container">
                    <div class="tab-row">
                        <button :disabled="isEditing" class="tab" @click.prevent="backToMenuList">
                            <span>Список меню</span>
                            <small>({{ $state.menus.length }})</small>
                        </button>
                        <h2 class="tab selected">
                            <span>Редактировать пункты</span>
                        </h2>
                    </div>
                </div>
                <div class="tab-page">
                    <h4 style="margin-bottom: 1rem;">
                        <span>{{ menu.title }}</span>
                        <small>({{ menu.name }}, {{ menu.id }})</small>
                    </h4>

                    <div v-if="!isEditing" class="form-group">
                        <div v-if="items.length" class="treeRoot">
                            <TreeNode v-for="(item, index) in items" :key="item.id"
                                :disabled="isEditing"
                                :index="index"
                                :node="item"
                                :parent-len="items.length"
                                @change="itemEdit"
                                @delete="itemDelete"
                                @order="itemOrder"
                            ></TreeNode>
                        </div>
                        <div v-else class="alert alert-warning">Пункты не созданы</div>
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
    </div>
</template>
