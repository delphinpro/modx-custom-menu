<!--
  Evo Custom Menu
  Copyright (c) 2018-2022
  delphinpro <delphinpro@yandex.ru>
  -->

<script>
export default {
    props: {
        node     : { type: Object },
        index    : { type: Number },
        indent   : { type: Number, default: 0 },
        parentLen: { type: Number, default: 0 },
        disabled : { type: Boolean, default: false },
    },

    data: () => ({}),

    computed: {
        icon() {
            return {
                'fa-file-o'     : !this.node.children.length,
                'fa-folder-open': this.node.children.length && !this.node.replaced,
                'fa-exchange'   : this.node.replaced,
            };
        },
        indentArr() {
            let arr = [];
            for (let i = 0; i < this.indent; i++) arr.push(1);
            return arr;
        },
    },

    methods: {
        onOrder(item, dir) {
            this.$emit('order', item, dir);
        },
    },
};
</script>

<template>
    <div>
        <a :class="{unpublished: node.isHide===1}"
            class="node"
        >
            <span class="indent"><i v-for="i in indentArr"></i></span>
            <span class="icon" @click.prevent="$emit('change', node)"><i :class="icon" class="fa"></i></span>
            <span class="title" @click.prevent="$emit('change', node)">
                <span v-if="!node.replaced">{{ node.title }}</span>
                <span
                    v-if="(node.title!==node.docTitle || node.replaced) && node.docTitle"
                    class="text-muted"
                >({{ node.docTitle }})</span>
            </span>
            <!--<small>(ID: {{node.id}})</small>-->
            <!--<small>(parentID: {{node.parentId}})</small>-->
            <!--<small>(children: {{node.children.length}})</small>-->
            <div class="btn-group">
                <!--<button class="btn btn-secondary btn-sm"-->
                <!--:disabled="disabled"-->
                <!--@click.prevent="$emit('change', node)"-->
                <!--&gt;Изменить пункт-->
                <!--</button>-->
                <button :disabled="disabled || index===0"
                    class="btn btn-secondary btn-sm"
                    @click.prevent="$emit('order', node, 1)"
                ><i class="fa fa-arrow-up"></i></button>
                <button class="btn btn-secondary btn-sm" disabled>{{ node.orderIndex }}</button>
                <button :disabled="disabled || index >= parentLen - 1"
                    class="btn btn-secondary btn-sm"
                    @click.prevent="$emit('order', node, 2)"
                ><i class="fa fa-arrow-down"></i></button>
                <button :disabled="disabled"
                    class="btn btn-outline-danger btn-sm"
                    @click.prevent="$emit('delete', node)"
                >Удалить пункт
                </button>
            </div>
        </a>
        <div v-if="node.children.length">
            <TreeNode v-for="(item, index) in node.children" :key="item.id"
                :disabled="disabled"
                :indent="indent + 1"
                :index="index"
                :node="item"
                :parent-len="node.children.length"
                @change="$emit('change', $event)"
                @delete="$emit('delete', $event)"
                @order="onOrder"
            ></TreeNode>
        </div>
    </div>
</template>
