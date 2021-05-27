<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Videos
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <card-video v-for="video in myVideos"
                        :video="video"
                        @delete_video="deleteVideo"
                    ></card-video>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import CardVideo from "@/Jetstream/Components/CardVideo";
export default {
name: "MyVideos",
    components: {
        CardVideo,
        AppLayout,
    },
    props:{
        myVideos:Array,
    },
    data(){
        return{
           videos:[]
        }
    },
    mounted() {
        this.videos = this.myVideos;
    },
    methods:{
        deleteVideo(id){
            console.log(id)
            const index = this.videos.findIndex(video=>{
                return +video.id === +id;
            })
            console.log('index - '+index);
            if(index>0){
                this.videos.splice(index,1);
            }
        }
    }
}
</script>

<style scoped>

</style>
