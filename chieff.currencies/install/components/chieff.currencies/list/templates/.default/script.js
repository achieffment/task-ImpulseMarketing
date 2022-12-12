var curUrl = new URL(window.location);
function changeSort(sort) {
    var param = curUrl.searchParams.get(sort);
    if (param !== null && param !== undefined) {
        if (param == "ASC")
            curUrl.searchParams.set(sort, "DESC")
        else
            curUrl.searchParams.delete(sort)
    } else
        curUrl.searchParams.append(sort, "ASC");
    window.location.href = curUrl.href;
}