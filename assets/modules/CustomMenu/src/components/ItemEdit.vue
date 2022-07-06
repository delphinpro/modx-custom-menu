<!--
  Evo Custom Menu
  Copyright (c) 2018-2022
  delphinpro <delphinpro@yandex.ru>
  -->
<script>
import { Http, url } from '../http';
import DocSelector from './DocSelector';

let timeoutId;

export default {
    components: {
        DocSelector,
    },

    props: {
        menu     : { type: Object, default: null },
        menuItem : { type: Object, default: null },
        menuItems: { type: Array, default: null },
    },

    data: () => ({
        item: {
            id        : null,
            parentId  : null,
            menuId    : null,
            orderIndex: 0,
            isHide    : 0,
            replaced  : 0,
            docId     : null,
            docTitle  : '',
            title     : '',
            url       : '',
            alias     : '',
        },

        parentSelectorShow: false,
    }),

    computed: {
        correctItem() {
            let hasTitle = (this.item.docTitle && this.item.docTitle.trim())
                || (this.item.title && this.item.title.trim());
            return this.item.menuId > 0
                && hasTitle
                ;
        },
        captionForm() {
            return this.item.id
                ? 'Редактирование пункта меню'
                : 'Добавление пункта меню'
                ;
        },
    },

    created() {
        console.log('ItemEdit created');
        if (this.menuItem === null) {
            this.item.menuId = this.menu.id;
        } else {
            this.item = { ...this.menuItem };
        }
    },

    methods: {
        loadResource() {
            if (this.item.docId) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    this.$store.clearErrors();
                    Http(url('getResource'), { docId: this.item.docId })
                        .then(res => {
                            // this.editedItem.docId = res.payload.resource.id;
                            this.item.docTitle = res.payload.resource.docTitle;
                            this.item.url = res.payload.resource.url;
                            this.item.alias = res.payload.resource.alias;
                        })
                        .catch(err => {
                            this.item.docTitle = null;
                            this.item.url = null;
                            this.item.alias = null;
                            this.$defaultError(err);
                        });
                }, 400);
            } else {
                this.item.docId = null;
                this.item.docTitle = '';
                this.item.url = '';
                this.item.alias = '';
            }
        },

        itemSave() {
            this.$store.clearErrors();
            if (!this.item.docId) this.item.docId = null;
            Http(url('itemSave'), this.item).then(res => {
                this.$emit('itemSave', res.payload.item);
            }).catch(this.$defaultError);
        },

        itemCancel() {
            this.$emit('cancel');
        },

        onPicked(node) {
            this.item.parentId = node.id;
            this.parentSelectorShow = false;
            // this.itemSave();
        },

        resetParent() {
            this.item.parentId = 0;
            this.itemSave();
        },

        clearDocId() {
            this.item.docId = null;
        },
    },

    watch: {
        'item.docId'(v) {
            console.log(v);
            if (!v) {
                this.item.replaced = 0;
                this.item.docTitle = '';
                this.item.alias = '';
                this.item.url = '';
            }
        },
    },
};
</script>

<template>
    <fieldset>
        <legend>{{ captionForm }}</legend>
        <div class="form-group">
            <table class="table-params">
                <tbody>
                    <tr>
                        <td><span>Название пункта</span></td>
                        <td><input v-model="item.title" :placeholder="item.docTitle" class="form-control" type="text">
                        </td>
                    </tr>
                    <tr>
                        <td><span>Родитель</span></td>
                        <td>
                            <div class="flex">
                                <button class="btn btn-secondary" @click="parentSelectorShow=true">Выбрать…</button>
                                <button class="btn btn-secondary" @click="resetParent">Сбросить</button>
                                <input v-model="item.parentId" class="form-control" readonly type="text">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span>Ресурс</span></td>
                        <td>
                            <div class="flex">
                                <input v-model.number="item.docId" class="form-control"
                                    placeholder="[~id~]"
                                    style="width: 60px; text-align:center;"
                                    type="text"
                                    @input="loadResource"
                                >
                                <button class="btn btn-secondary" @click="clearDocId">Очистить</button>
                                <input :value="item.docTitle" class="form-control" placeholder="" readonly type="text">
                                <button class="btn btn-secondary" disabled>Выбрать…</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!item.replaced">
                        <td><span>Ссылка</span></td>
                        <td><input v-model="item.url" class="form-control" placeholder="" type="text"></td>
                    </tr>
                    <tr v-if="item.docId && item.docTitle">
                        <td><span>Замещаемый элемент</span></td>
                        <td>
                            <div class="flex">
                                <select class="form-control"
                                    style="max-width: 60px;"
                                    @change="item.replaced = +$event.target.value"
                                >
                                    <option :key="0" :selected="item.replaced===0" :value="0">Нет</option>
                                    <option :key="1" :selected="item.replaced===1" :value="1">Да</option>
                                </select>
                                <span class="text-muted" style="padding-left: 1rem; line-height:15px">
                                    Замещаемый элемент непосредственно не отображается в меню.
                                    Он замещает себя ссылками на дочерние страницы выбранного ресурса.
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span>Скрыть этот пункт</span></td>
                        <td>
                            <div class="flex">
                                <select class="form-control"
                                    style="max-width: 60px;"
                                    @change="item.isHide = +$event.target.value"
                                >
                                    <option :key="0" :selected="item.isHide===0" :value="0">Нет</option>
                                    <option :key="1" :selected="item.isHide===1" :value="1">Да</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><span>MenuID / ItemID</span></td>
                        <td>
                            <input v-model="item.menuId" class="form-control" placeholder=""
                                readonly style="width: 60px;" type="text"
                            >
                            <span class="form-control-static">/</span>
                            <input v-model="item.id" class="form-control" placeholder=""
                                readonly style="width: 60px;" type="text"
                            >
                        </td>
                    </tr>
                    <tr v-if="!item.replaced">
                        <td><span>Alias</span></td>
                        <td><input v-model="item.alias" class="form-control" placeholder="" readonly type="text"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group d-flex">
            <div class="btn-group">
                <button :class="{
            'btn-success': correctItem,
            'btn-secondary': !correctItem
        }" :disabled="!correctItem" class="btn" @click.prevent="itemSave"
                >Сохранить
                </button>
                <button class="btn btn-secondary" @click.prevent="itemCancel">Отмена</button>
            </div>
        </div>
        <DocSelector
            v-if="parentSelectorShow"
            :current-node="item"
            :tree="menuItems"
            title="Выберите родительский пункт для редактируемого"
            width="700px"
            @close="parentSelectorShow=false"
            @picked="onPicked"
        ></DocSelector>
    </fieldset>
</template>
