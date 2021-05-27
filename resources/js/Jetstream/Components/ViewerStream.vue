<template>
    <div class="row-auto">

        <div class="row-span-full" v-if="!isShowVideo">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" @click.prevent="refreshListDevices">
                <span class="text-center">Refresh list media devices</span>
            </button>
            <div class="col-6">
                <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        v-model="constraints.audio.deviceId.exact">
                    <option selected="selected" disabled>Select audio device</option>
                    <option v-for="audioDevice in audioDevices" :value="audioDevice.deviceId">{{ audioDevice.label }}</option>
                </select>
            </div>
            <div class="col-6">
                <select class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                        v-model="constraints.video.deviceId.exact">
                    <option selected="selected" disabled>Select video device</option>
                    <option v-for="videoDevice in videoDevices" :value="videoDevice.deviceId">{{ videoDevice.label }}</option>
                </select>
            </div>
        </div>

    </div>

<!--    <span class="text-center" :class="classErrorMessage" v-if="error_message">{{error_message}}</span>-->

    <div class="row-auto">
        <video class="" id="videoPlayer" autoplay muted playsinline></video>
    </div>
    <div class="inline-flex">
        <button class="bg-green-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                @click.prevent="startRecordVideo" v-if="!isShowVideo">Start</button>
        <button class="bg-red-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                @click.prevent="stopVideo" v-else>Stop</button>
    </div>

<!--        <button class="button button_xs button_primary" @click.prevent="enableView">Enable</button>-->
<!--        <button class="button button_xs button_secondary" @click.prevent="disableView">Disable</button>-->
</template>

<script>
import Button from "@/Jetstream/Button";
export default {
    name: "ViewerStream",
    components: {Button},
    props:[],
    data(){
        return{
            currentUserStream: false,
            constraints:{
                audio: {
                    deviceId:{
                        exact:null
                    }
                },
                video: {
                    deviceId:{
                        exact:null
                    }
                },
            },
            audioDevices: [],
            videoDevices: [],
            error_message:'',
            countGetUserMedia:0,
            yourVideo:null,
            mediaRecorder:null,
            isShowVideo:false,
            nameVideo:'',
        }
    },
    computed: {
        classErrorMessage: function () {
            return {
                'text-danger': this.error_message !== 'Please select a media device from the list and start streaming.',
                'text-success': this.error_message === 'Please select a media device from the list and start streaming.'
            }
        },
    },
    mounted() {
        this.yourVideo = document.getElementById('videoPlayer')
        this.getMediaDevises();
        // this.showMyFace();
    },
    methods:{
        startRecordVideo(){
            axios.get('/start-record-video')
                .then(response=>{
                    console.log(response.data);
                    let srcArr = response.data.src.split('/')
                    this.nameVideo = srcArr.pop();
                    this.showMyFace();
                })
        },
        async showMyFace() {
            console.log(this.nameVideo);
            this.isShowVideo = true;

            try {
                // get a local stream, show it in a self-view and add it to be sent
                if(navigator.mediaDevices){
                    console.log('in showMyFace');
                    const stream = await navigator.mediaDevices.getUserMedia(this.constraints);
                    this.yourVideo.srcObject = stream;
                    const recTime = 10;
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.mediaRecorder.ondataavailable = e => {
                        // fetch("api.php", {
                        //     method: "POST",
                        //     headers: {"Content-Type": "video/webm", "X-PWD": pwd},
                        //     body: d.data
                        // })
                        console.log('save video');
                        console.log(e);
                        axios.post(`/save-video/${this.nameVideo}`,
                            e.data,
                            {
                                headers: {
                                    'Content-Type': "video/webm",
                                }
                            })
                        .then(response=>{
                            console.log(response.data);
                            if(response.data.status !== 'ok'){
                                alert('Error save video');
                                this.stopVideo();
                            }
                        })
                    };
                    this.mediaRecorder.start(recTime * 1000);
                }else {
                    alert('error get user media')
                }

                // stream.getTracks().forEach((track) => this.pc.addTrack(track, stream));
                // yourVideo.srcObject = stream;
            } catch (err) {
                console.error('ошибка вебкамера....'+err);
            }

        },
        stopVideo(){
            this.isShowVideo = false;
            this.yourVideo.srcObject.getTracks().forEach(track => track.stop());
            this.yourVideo.srcObject = null;
            this.mediaRecorder.stop();
        },
        enableView(){
            let constraints = null ;
            if(this.constraints.video.deviceId.exact && this.constraints.audio.deviceId.exact){
                constraints = this.constraints ;
            }
            let data = {
                video:document.getElementById("player-owner"),
                constraints:constraints,
                callbackSuccess:this.callbackSuccess,
                callbackError:this.callbackError
            };
            console.log('enable viewer video ');
            this.currentUserStream = true;
            this.$emit('enableviewervideo', data);
        },
        disableView(){
            console.log('disable viewer video ');
            this.currentUserStream = false;
            this.$emit('disableviewervideo');
        },
        getUserMedia(){
            this.countGetUserMedia++;
            let constraints = {
                audio: true,
                video: true
            };
            navigator.mediaDevices.getUserMedia(constraints)
                .then(stream=>{
                    stream.getTracks().forEach(track =>{
                        track.stop();
                    });

                    console.log('Получил поток с ограничениями:', constraints);
                    this.refreshListDevices();
                    this.error_message ='Please select a media device from the list and start streaming.';

                })
                .catch(error => {
                    if (error.name === 'ConstraintNotSatisfiedError') {
                        alert('Разрешение ' + constraints.video.width.exact + 'x' +
                            constraints.video.height.exact + ' px не поддерживается устройством.');
                    } else if (error.name === 'PermissionDeniedError') {
                        alert('Разрешения на использование камеры и микрофона не были предоставлены. ' +
                            'Вам нужно разрешить странице доступ к вашим устройствам,' +
                            ' чтобы демо-версия работала.');
                    }
                    this.errorMsg('getUserMedia error: ' + error.name, error);
                });



        },
        errorMsg(msg, error) {
            this.error_message = 'You have not given permission to use media devices in your browser';
            console.error(error);
        },

        refreshListDevices(){
            this.getMediaDevises();
        },
        getMediaDevises(){
            navigator.mediaDevices.enumerateDevices()
                .then(devices=>{
                    console.log('Promise mediaDevices.enumerateDevices');
                    console.log(devices);
                    this.deviceHandler(devices);
                })
                .catch(error=>{
                    console.log(error) ;
                    this.error_message = 'The browser does not currently support media device selection';
                });

        },
        deviceHandler(devices){
            let isEmptyLabel = false ;
            if (devices.length > 0 ) {
                let counterCamera = 1;
                let counterMicrophone = 1;
                this.audioDevices = [];
                this.videoDevices = [];
                devices.forEach(device => {
                    if (device.kind === 'audioinput' && device.deviceId !== '') {
                        let audioEl = {
                            label: device.label,
                            deviceId: device.deviceId
                        };
                        if (audioEl.label === '') {
                            isEmptyLabel = true ;
                            audioEl.label = 'Microphone ' + counterMicrophone;
                            counterMicrophone++;
                        }
                        this.audioDevices.push(audioEl);
                    }

                    if (device.kind === 'videoinput' && device.deviceId !== '') {
                        let videoEl = {
                            label: device.label,
                            deviceId: device.deviceId
                        };
                        if (videoEl.label === '') {
                            isEmptyLabel = true ;
                            videoEl.label = 'Camera ' + counterCamera;
                            counterCamera++;
                        }
                        this.videoDevices.push(videoEl);
                    }
                });
                if (this.audioDevices.length > 0){
                    this.constraints.audio.deviceId.exact = this.audioDevices[0].deviceId;
                }
                if (this.videoDevices.length > 0){
                    this.constraints.video.deviceId.exact = this.videoDevices[0].deviceId;
                }
            }
            if (this.countGetUserMedia === 0 &&
                (isEmptyLabel || (devices && devices.length === 0) || this.audioDevices.length === 0 || this.videoDevices ===0)){
                this.getUserMedia();
                this.error_message = 'Allow the use of media devices in your browser' ;
            }
        },
        muteAudio(){
            this.Streamer.muteAudio();
            console.log('muteAudio');
        },
        callbackSuccess(){
            console.log('callbackSuccess()');
        },
        callbackError(callbackMessage){
            this.countGetUserMedia = 0;
            console.log('callbackError');
            console.log(callbackMessage);
            this.error_message = callbackMessage.error_message ;
        },
    }
}
</script>

<style scoped>

</style>
