<!--
  Evo Custom Menu
  Copyright (c) 2018-2022
  delphinpro <delphinpro@yandex.ru>
  -->

<script>
export default {
    props: {
        menu: { type: Object },
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
        <legend>{{ captionForm }}</legend>

        <div class="form-group">
            <table class="table-params">
                <tbody>
                    <tr>
                        <td><span>MenuID</span></td>
                        <td><input v-model.number="menuDef.id" class="form-control" readonly type="text"></td>
                    </tr>
                    <tr>
                        <td><span>Имя меню *</span></td>
                        <td><input v-model="menuDef.name" class="form-control" type="text"></td>
                    </tr>
                    <tr>
                        <td><span>Название меню *</span></td>
                        <td><input v-model="menuDef.title" class="form-control" type="text"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="form-group">
            <div class="btn-group">
                <button :disabled="buttonSaveDisabled" class="btn btn-success" @click.prevent="menuSave">
                    Сохранить
                </button>
                <button class="btn btn-secondary" @click.prevent="menuCancel">Отмена</button>
            </div>
        </div>
    </fieldset>
</template>
