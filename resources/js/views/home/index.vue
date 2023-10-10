<template>
  <!--    <div class="main" style="background-color: red">-->

  <div class="container mt-3">
    <div class="title">
      <h3> Welcome to <img width="200" height="50" src="/images/ue_logo_black_horizontal.png" alt="UE Logo"/> Code Kits</h3>

    </div>
    <div class="main">
      <p>Unreal code kits aims to allow the Unreal community to share complete pieces of code that
        you can use in your Unreal Engine project.
      </p>
    </div>
        <video id="background-video" ref="video" autoplay muted width="200px" height="200px">
          <source src="/images/UE_moving_logo_v03_1080.mp4" type="video/mp4">
        </video>
  </div>


</template>

<script setup>
import {onMounted} from 'vue';

onMounted(() => {
  // let video = document.getElementById("background-video");
  // console.log(video)
  // video.onended = function(){
  //   video.style.display = "none";
  // }
})

</script>

<style scoped>
.wrapper {
  display: flex;
  height: 100%;
}

.title {
  display: flex;
  justify-content: center;
}

.main {
  display: flex;
  justify-content: center;
}
</style>
