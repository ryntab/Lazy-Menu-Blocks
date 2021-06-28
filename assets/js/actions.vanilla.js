document.addEventListener("DOMContentLoaded", function(){
    let hasBlock = document.getElementsByClassName('im-lazy');
    for (var i = 0; i < hasBlock.length; i++) {
        hasBlock.item(i).onmouseenter = function() {
            if (this.classList.contains('loaded')) return;
            this.blockID = this.children[0].getAttribute('data-block');
            let item = this;
            fetcher(item);
        }
    }

    const fetcher = (item) => {
        console.log('fetcher run');
        fetch(`${window.location.origin}/wp-json/lazyMenu/UX/block/${item.blockID}`)
            .then(response => response.json())
            .then(data => {
                item.classList.add('loaded');
                item.querySelectorAll('.sub-menu')[0].innerHTML = data.block;
                spinner = item.getElementsByClassName('loading-spin');
                spinner[0].remove();
        });
     }
});
