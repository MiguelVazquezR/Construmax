<template>
    <div>
        <header class="border border-b-0 border-gray3 rounded-tl-[3px] rounded-tr-[3px] h-7 flex items-center">
            <button @click="toggleStyle('bold')" :class="{ 'text-primary': styles.bold }" type="button"
                class="border-r border-gray3 px-3 text-sm">
                <i class="fa-solid fa-bold"></i>
            </button>
            <button @click="toggleStyle('italic')" :class="{ 'text-primary': styles.italic }" type="button"
                class="border-r border-gray3 px-3 text-sm">
                <i class="fa-solid fa-italic"></i>
            </button>
            <button @click="toggleStyle('underline')" :class="{ 'text-primary': styles.underline }" type="button"
                class="border-r border-gray3 px-3 text-sm">
                <i class="fa-solid fa-underline"></i>
            </button>
        </header>
        <div contenteditable="true" @input="updateContent" ref="editor" id="editor"
            class="input rounded-tr-none rounded-tl-none min-h-[80px] p-2 focus:outline-none"></div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            styles: {
                bold: false,
                italic: false,
                underline: false
            }
        };
    },
    emits: ['content'],
    methods: {
        toggleStyle(style) {
            // Cambia el estado del estilo
            this.styles[style] = !this.styles[style];

            // Aplica el estilo seleccionado al texto seleccionado o al texto que se escribirá en el futuro
            document.execCommand(style, false, null);
            // Enfoca nuevamente el editor de texto después de aplicar el estilo
            this.$refs.editor.focus();
        },
        updateContent() {
            // Actualiza el contenido del editor
            this.$emit('content', this.$refs.editor.innerHTML);
        }
    }
}
</script>
<!-- <style scoped>
/* Establece el color de fondo y el color de texto para el texto seleccionado */
#editor::selection {
    background-color: #FD8827;
    /* Cambia el color de fondo de selección a tu preferencia */
    color: #000000;
    /* Cambia el color del texto seleccionado a tu preferencia */
}
</style> -->