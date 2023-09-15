<!--<script setup>-->
<!--import {ref} from "vue";-->

<!--const fileInputRef = ref(null);-->
<!--const handleFileChange = () => {-->
<!--    console.log('File changed!');-->
<!--};-->

<!--const browseImage = () => {-->
<!--    if (fileInputRef.value) {-->
<!--        fileInputRef.value.click(); // Call any method on the DOM element here-->
<!--    }-->
<!--};-->
<!--</script>-->

<!--<template>-->
<!--    <div>-->
<!--        <input type="file" accept="image/*" class="hidden" @change="handleFileChange">-->
<!--        <button @click.stop="browseImage">Browse</button>-->
<!--    </div>-->
<!--</template>-->

<script>
import {CameraIcon, XIcon} from "@heroicons/vue/outline";
export default {
    components: {CameraIcon, XIcon},
    props: {
        value: File,
        defaultSrc: String
    },
    data() {
      return {
          src: this.defaultSrc,
          file: null
      }
    },
    computed: {
        iconSizeClasses() {
            return 'w-6 h-6';
        }
    },
    methods: {
        browse() {
            this.$refs.file.click();
        },
        remove() {
            this.file = null;
            this.src = this.defaultSrc
            this.$emit('update:modelValue', this.file)
        },
        change(e) {
            this.file = e.target.files[0];
            this.$emit('update:modelValue', this.file)
            let reader = new FileReader();
            reader.readAsDataURL(this.file);
            reader.onload = (e) => {
                this.src = e.target.result;
            }
        }
    }
}

</script>

<template>
    <div class="relative inline-block overflow-hidden">
        <input type="file" accept="image/*" class="hidden" ref="file" @change="change">
        <img :src="src" alt="Avatar" class="h-full w-full object-cover">
        <div class="absolute top-0 h-full w-full bg-black bg-opacity-25 flex justify-center items-center">
            <button @click.prevent="browse()" class="rounded-full text-white hover:bg-white hover:bg-opacity-25 p-2 focus:outline-none transition duration-200">
                <CameraIcon aria-hidden="true" :class="iconSizeClasses" />
            </button>
            <button v-if="file" @click.prevent="remove()" class="rounded-full text-white hover:bg-white hover:bg-opacity-25 p-2 focus:outline-none transition duration-200">
                <XIcon aria-hidden="true" :class="iconSizeClasses" />
            </button>
        </div>
    </div>
</template>
