<template>
    <div id="login">
        <div class="container">
            <router-view />
        </div>
    </div>
</template>

<script>
  export default {
    name: 'simpleLayout'     // you can enter any name (optional)
  }
</script>

<style>
    #login{
        min-height:100vh;
    }
</style>