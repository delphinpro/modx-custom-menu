<script>/**
 * delphinpro.mysite
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

export default {
    props: {
        node       : {type: Object},
        indent     : {type: Number, default: 0},
        currentNode: {type: Object, default: null},
    },

    data: () => ({}),

    computed: {
        iconClass() {
            return {
                'fa-file-o'     : !this.node.children.length,
                'fa-folder-open': this.node.children.length && !this.node.replaced,
                'fa-exchange'   : this.node.replaced,
            };
        },

        nodeClasses() {
            return {
                unpublished: this.node.isHide === 1,
                hidemenu   : this.disabledNode,
            };
        },

        disabledNode() {
            return (
                this.node.id === this.currentNode.id
                || this.node.id === this.currentNode.parentId
                || this.node.replaced === 1
            );
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
        <a class="node" :class="nodeClasses">
            <span class="indent"><i v-for="i in indentArr"></i></span>
            <span class="icon"><i class="fa" :class="iconClass"></i></span>
            <span class="title" @click.prevent="!disabledNode && $emit('picked', node)">
                <span v-if="node.replaced">{{node.docTitle}}</span>
                <span v-else>{{node.title}}</span>
            </span>
        </a>
        <div v-if="node.children.length">
            <DocSelectorNode v-for="(item, index) in node.children" :key="item.id"
                :node="item"
                :indent="indent + 1"
                :current-node="currentNode"
                @picked="$emit('picked', $event)"
            ></DocSelectorNode>
        </div>
    </div>
</template>
