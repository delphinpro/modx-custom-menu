<script>/**
 * delphinpro.mysite
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

export default {
    props: {
        title      : {type: String, default: 'Title'},
        width      : {type: String, default: '600px'},
        tree       : {type: Array, default: () => []},
        currentNode: {type: Object, default: null},
    },

    data: () => ({
        ready: false,
    }),

    computed: {
        styles() {
            return {
                width: this.width,
            };
        },
    },

    mounted() {
        this.ready = true;
    },
};
</script>

<template>
    <transition name="cm-fade">
        <div class="cm-modal-container" @click.self="$emit('close')">
            <transition name="cm-modal">
                <div class="cm-modal" v-if="ready" :style="styles">
                    <div class="cm-modal__close close" @click.prevent="$emit('close')">×</div>
                    <div class="cm-modal__header">
                        <i class="fa fa-info"></i>{{title}}
                    </div>
                    <div class="cm-modal__body">
                        <div class="treeRoot" v-if="tree.length">
                            <DocSelectorNode v-for="(item) in tree" :key="item.id"
                                :node="item"
                                :current-node="currentNode"
                                @picked="$emit('picked', $event)"
                            ></DocSelectorNode>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>

<style lang="scss">
    .cm-modal {
        &-enter { transform: scale(0.5); opacity: 0; }
        &-enter-active { transition: 0.5s ease; }
        &-enter-to { transform: scale(1); opacity: 1; }
    }
    .cm-fade {
        &-enter { opacity: 0; }
        &-enter-active { transition: 0.3s ease; }
        &-enter-to { opacity: 1; }
    }

    .cm-modal-container {
        position: fixed;
        z-index: 10600;
        right: 0;
        top: 0;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        padding: 40px 0;
        background: rgba(#000, 0.4);
    }

    .cm-modal {
        position: relative;
        width: 500px;
        max-width: 90%;
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.3) !important;
        margin: auto;
        color: #444444;
        border-radius: .15rem;
        padding: 0;
        border: none;
        background-color: #ecf0f1;

        .darkness & {
            background-color: #282c34;
        }

        &__close {
            position: absolute;
            z-index: 9;
            opacity: 1;
            top: 0;
            right: 0;
            width: 1.5rem;
            height: 1.5rem;
            line-height: 1.4rem;
            color: #ffffff;
            background-color: #e5a2a0;
            text-align: center;
            font-weight: 100;

            .darkness & {
                opacity: 0.64;
                background-color: #ca000c;
                font-family: serif;
            }
        }

        &__header {
            position: relative;
            height: 1.5rem;
            margin: 0;
            padding: 0 1.5rem 0 .5rem;
            line-height: 1.5rem;
            font-size: .9rem;
            border-bottom: 1px solid #dedede;

            .fa { margin-right: 0.5em; }

            .darkness & {
                border-bottom: 1px solid #17191d;
                color: #eeeeee;
            }
        }

        &__body {
            padding: 1rem;
        }
    }

</style>
