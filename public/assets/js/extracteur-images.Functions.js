export { downloadFiles, displayDownloadLink }

function displayDownloadLink(link, responseContainer){
    responseContainer.innerHTML = `
    <div class="thor">
            <div class="left-wings">
                <div class="wing-1"></div>
                <div class="wing-2"></div>
                <div class="wing-3"></div>
            </div>

            <div class="right-wings">
                <div class="wing-4"></div>
                <div class="wing-5"></div>
                <div class="wing-6"></div>
            </div>
            <div class="helmet">
                <div class="helmet-detail"></div>
            </div>
            <div class="head">
                <div class="left-eye"></div>
                <div class="right-eye"></div>
                <div class="mouth"></div>
            </div>
            <div class="body">
                <div class="button-1"></div>
                <div class="button-2"></div>
                <div class="button-3"></div>
                <div class="button-4"></div>
            </div>
            <div class="hammer-c">
                <div class="hammer"></div>
            </div>
            <div class="left-hand"></div>
            <div class="right-hand"></div>
            <div class="shadow"></div>
        </div>
        <a class="btn btn-lg btn-primary mt-3 allWaButton" href="${link.zipFile}">Votre zip ici</a>
    `;
}

async function downloadFiles(data, siteName, options, responseContainer) {
    fetch("/dl_file/" + siteName, {
      ...options,
      body: JSON.stringify({ data }),
    })
      .then((res) => {
        return res.json();
      })
      .then((dataRes) => {
        fetch("/make_zip/" + siteName, {
          ...options,
          body: JSON.stringify({ dataRes }),
        })
        .then((res)=> { return res.json()})
        .then((data) => {
            displayDownloadLink(data, responseContainer)
        })
        .catch((err) => {
        console.log("making zip fail: ", err);

        });
      })
      .catch((err) => {
        console.log("download fail: ", err);
      });
  }