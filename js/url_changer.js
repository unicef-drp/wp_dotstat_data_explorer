(function () {
/*
document.getElementById('root').addEventListener('unicef_dataflowLoaded', function (evt) {
    searchTerms = evt.detail.dataflow.agencyId + " " + evt.detail.data.structure.name;
    searchTerms = searchTerms + " " + getAdditionalKeywordsForSearch(evt.detail);

    dataq = evt.detail.dataflow.dataquery;
    if (dataq.endsWith("/")) { dataq = dataq.substring(0, dataq.length - 1); }
    let df = evt.detail.dataflow

    currentid = df.datasourceId + "|" + df.agencyId + "|" + df.dataflowId + "|" + df.version + "|" + dataq;
    window.history.pushState({ 'ag': df.agencyId, 'df': df.dataflowId, 'ver': df.version, 'dq': dataq },
        df.dataflowId,
        `?ag=${df.agencyId}&df=${df.dataflowId}&ver=${df.version}&dq=${dataq}&startPeriod=${df.period[0]}&endPeriod=${df.period[1]}`);
    //http://localhost/resources/data_explorer/unicef_f/?ag=UNICEF&df=CHLD_PVTY&ver=1.0&dq=.PV.&startPeriod=2015&endPeriod=2019
    //http://localhost/resources/data_explorer/unicef_f/page2.php
    search_related(searchTerms, currentid);
});
*/

document.getElementById('root').addEventListener('unicef_dataflowLoaded',function(evt){
    let url = window.location.href;
    //replace the trainling / if present
    let newDq=evt.detail.dataquery.replace(/\/$/,"");
    let urlRepl=url.replace(/dq=[^&]*/, "dq="+newDq);
    urlRepl=urlRepl.replace(/startPeriod=[^&]*/, "startPeriod="+evt.detail.requestArgs.startPeriod);
    urlRepl=urlRepl.replace(/endPeriod=[^&]*/, "endPeriod="+evt.detail.requestArgs.endPeriod);
    window.history.pushState({}, "UNICEF Data", urlRepl);
});

})();