<script>/**
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

export default {
    props: {
        menu: {type: Object},
    },

    data: () => ({
        menuDef: {
            id   : null,
            name : null,
            title: '',
        },
    }),

    computed: {
        buttonSaveDisabled() {
            return !this.menuDef.name
                || this.menuDef.name.trim() === ''
                || this.menuDef.title.trim() === '';
        },

        captionForm() {
            return this.menuDef.id
                ? 'Редактирование меню'
                : 'Новое меню'
                ;
        },
    },

    created() {
        this.menuDef = {
            id   : this.menu.id,
            name : this.menu.name,
            title: this.menu.title,
        };
    },

    methods: {
        menuSave() {
            this.$emit('save', this.menuDef);
        },

        menuCancel() {
            this.$emit('cancel');
        },
    },
};
</script>

<template>
    <fieldset>
        <legend>{{captionForm}}</legend>

        <div class="form-group">
            <table class="table-params">
                <tbody>
                    <tr>
                        <td><span>MenuID</span></td>
                        <td><input class="form-control" type="text" v-model.number="menuDef.id" readonly></td>
                    </tr>
                    <tr>
                        <td><span>Имя меню *</span></td>
                        <td><input class="form-control" type="text" v-model="menuDef.name"></td>
                    </tr>
                    <tr>
                        <td><span>Название меню *</span></td>
                        <td><input class="form-control" type="text" v-model="menuDef.title"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <div class="btn-group">
                <button class="btn btn-success" @click.prevent="menuSave" :disabled="buttonSaveDisabled">
                    Сохранить
                </button>
                <button class="btn btn-secondary" @click.prevent="menuCancel">Отмена</button>
            </div>
        </div>
    </fieldset>
</template>
