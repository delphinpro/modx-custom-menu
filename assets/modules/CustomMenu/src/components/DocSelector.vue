<!--
  Evo Custom Menu
  Copyright (c) 2018-2022
  delphinpro <delphinpro@yandex.ru>
  -->

<script>
export default {
    props: {
        title      : { type: String, default: 'Title' },
        width      : { type: String, default: '600px' },
        tree       : { type: Array, default: () => [] },
        currentNode: { type: Object, default: null },
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
                <div v-if="ready" :style="styles" class="cm-modal">
                    <div class="cm-modal__close close" @click.prevent="$emit('close')">×</div>
                    <div class="cm-modal__header">
                        <i class="fa fa-info"></i>{{ title }}
                    </div>
                    <div class="cm-modal__body">
                        <div v-if="tree.length" class="treeRoot">
                            <DocSelectorNode v-for="(item) in tree" :key="item.id"
                                :current-node="currentNode"
                                :node="item"
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
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    overflow: auto;
    width: 100%;
    height: 100%;
    padding: 40px 0;
    background: rgba(#000, 0.4);
}

.cm-modal {
    position: relative;
    width: 500px;
    max-width: 90%;
    margin: auto;
    padding: 0;
    color: #444;
    border: none;
    border-radius: .15rem;
    background-color: #ecf0f1;
    box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.3) !important;

    .darkness & {
        background-color: #282c34;
    }

    &__close {
        font-weight: 100;
        line-height: 1.4rem;
        position: absolute;
        z-index: 9;
        top: 0;
        right: 0;
        width: 1.5rem;
        height: 1.5rem;
        text-align: center;
        opacity: 1;
        color: #fff;
        background-color: #e5a2a0;

        .darkness & {
            font-family: serif;
            opacity: 0.64;
            background-color: #ca000c;
        }
    }

    &__header {
        font-size: .9rem;
        line-height: 1.5rem;
        position: relative;
        height: 1.5rem;
        margin: 0;
        padding: 0 1.5rem 0 .5rem;
        border-bottom: 1px solid #dedede;

        .fa { margin-right: 0.5em; }

        .darkness & {
            color: #eee;
            border-bottom: 1px solid #17191d;
        }
    }

    &__body {
        padding: 1rem;
    }
}
</style>
