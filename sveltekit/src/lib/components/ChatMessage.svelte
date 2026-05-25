<script lang="ts">
    interface Msg {
        fromMe: boolean;
        text: string;
        time: string;
    }

    interface Props {
        message: Msg;
    }

    let { message }: Props = $props();
</script>

<div class="message" class:from-me={message.fromMe}>
    <div class="msg-bubble">{message.text}</div>
    <div class="msg-time">{message.time}</div>
</div>

<style>
    .message {
        max-width: 70%;
        align-self: flex-start;
    }

    .message.from-me {
        align-self: flex-end;
    }

    .msg-bubble {
        padding: 12px 16px;
        border-radius: 18px;
        background: #e5efff;
        line-height: 1.4;
    }

    .message.from-me .msg-bubble {
        background: #2b6cb0;
        color: white;
    }

    .msg-time {
        font-size: 0.75rem;
        color: #aaa;
        margin-top: 4px;
        text-align: right;
    }
</style>
