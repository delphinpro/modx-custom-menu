<script>/**
 * delphinpro.mysite
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

export default {
    props: {
        node     : {type: Object},
        index    : {type: Number},
        indent   : {type: Number, default: 0},
        parentLen: {type: Number, default: 0},
        disabled : {type: Boolean, default: false},
    },

    data: () => ({}),

    computed: {
        icon() {
            return {
                'fa-file-o'     : !this.node.children.length,
                'fa-folder-open': this.node.children.length && !this.node.replaced,
                'fa-exchange': this.node.replaced,
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
        <a class="node"
            :class="{unpublished: node.isHide===1}"
        >
            <span class="indent"><i v-for="i in indentArr"></i></span>
            <span class="icon" @click.prevent="$emit('change', node)"><i class="fa" :class="icon"></i></span>
            <span class="title" @click.prevent="$emit('change', node)">
                <span v-if="!node.replaced">{{node.title}}</span>
                <span
                    class="text-muted"
                    v-if="node.title!==node.docTitle || node.replaced"
                >({{node.docTitle}})</span>
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
                <button class="btn btn-secondary btn-sm"
                    :disabled="disabled || index===0"
                    @click.prevent="$emit('order', node, 1)"
                ><i class="fa fa-arrow-up"></i></button>
                <button class="btn btn-secondary btn-sm" disabled>{{node.orderIndex}}</button>
                <button class="btn btn-secondary btn-sm"
                    :disabled="disabled || index >= parentLen - 1"
                    @click.prevent="$emit('order', node, 2)"
                ><i class="fa fa-arrow-down"></i></button>
                <button class="btn btn-outline-danger btn-sm"
                    :disabled="disabled"
                    @click.prevent="$emit('delete', node)"
                >Удалить пункт
                </button>
            </div>
        </a>
        <div v-if="node.children.length">
            <TreeNode v-for="(item, index) in node.children" :key="item.id"
                :node="item"
                :index="index"
                :indent="indent + 1"
                :parent-len="node.children.length"
                :disabled="disabled"
                @change="$emit('change', $event)"
                @order="onOrder"
                @delete="$emit('delete', $event)"
            ></TreeNode>
        </div>
    </div>
</template>
