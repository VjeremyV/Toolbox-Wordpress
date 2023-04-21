export { downloadFiles, displayDownloadLink }

function displayDownloadLink(link, responseContainer){
    responseContainer.innerHTML = `<a href="${link.zipFile}">Votre zip ici</a>`;
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