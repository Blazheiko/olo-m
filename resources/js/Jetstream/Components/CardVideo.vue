<template>
    <div class="max-w-sm rounded overflow-hidden shadow-lg">
        <video class="w-full" controls>
            <source :src="`/storage/${video.src}`">
        </video>
        <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2">
                {{new Date(video.created_at).toLocaleDateString()}} time {{new Date(video.created_at).toLocaleTimeString()}}
            </div>
            <p class="text-gray-700 text-base">

            </p>
        </div>
        <div class="px-6 pt-4 pb-2">
            <span class="py-2 px-4 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-75"
                @click.prevent="deleteVideo">delete</span>
<!--            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">download</span>-->
<!--            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#winter</span>-->
        </div>
    </div>
</template>

<script>
export default {
name: "CardVideo",
    props:['video'],
    methods:{
        deleteVideo(){
            if(confirm(`Вы точно хотите удалить видео ${new Date(this.video.created_at).toLocaleDateString()} time ${new Date(this.video.created_at).toLocaleTimeString()}`)){
                console.log('start delete video')
                axios.get(`/delete-video/${this.video.id}`)
                    .then(response=>{
                        console.log(response.data);
                        console.log(this.video.id);
                        this.$emit('delete_video',this.video.id);
                    })
                    .catch(err=>{
                        console.error(err)
                        alert('Error delete video');
                    })
            }
        }
    }
}
</script>

<style scoped>

</style>
