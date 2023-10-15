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
        },
      };
    },
    props: {
      defaultValue: {
        type: String,
        default: ''
      },
    },
    emits: ['content'],
    mounted() {
      // Establecer el contenido inicial del editor con el valor por defecto y aplicar estilos si es necesario
      if (this.defaultValue) {
        this.$refs.editor.innerHTML = this.defaultValue;
        this.updateContent();
      }
    },
    methods: {
      toggleStyle(style) {
        this.$refs.editor.focus();
        this.styles[style] = !this.styles[style];
        document.execCommand(style, false, null);
        this.$refs.editor.focus();
        this.updateContent();
      },
      updateContent() {
        // Obtén el contenido del editor
        const contentWithHTML = this.$refs.editor.innerHTML;
  
        // Envuelve el contenido con etiquetas <div>
        const contentWithDiv = `<div>${contentWithHTML}</div>`;
  
        // Emite el contenido del editor con las etiquetas <div>
        this.$emit('content', contentWithDiv);
      },
    }
  }
  </script>
  