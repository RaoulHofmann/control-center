export interface AlternateTitle {
    title: string,
    seasonNumber?: number,
    sceneSeasonNumber?: number,
    sceneOrigin?: string,
    comment?: string
}

export interface Image {
    coverType: string,
    url: string,
    remoteUrl?: string
}

export interface Language {
    id: number,
    name: string
}

export interface SeasonStatistics {
    nextAiring?: string,
    previousAiring?: string,
    episodeFileCount: number,
    episodeCount: number,
    totalEpisodeCount: number,
    sizeOnDisk: number,
    releaseGroups: string[],
    percentOfEpisodes: number
}

export interface Season {
    seasonNumber: number,
    monitored: boolean,
    statistics: SeasonStatistics,
    images: Image[]
}

export interface AddOptions {
    ignoreEpisodesWithFiles: boolean,
    ignoreEpisodesWithoutFiles: boolean,
    monitor: string,
    searchForMissingEpisodes: boolean,
    searchForCutoffUnmetEpisodes: boolean
}

export interface Ratings {
    votes: number,
    value: number
}

export interface Statistics {
    seasonCount: number,
    episodeFileCount: number,
    episodeCount: number,
    totalEpisodeCount: number,
    sizeOnDisk: number,
    releaseGroups: string[],
    percentOfEpisodes: number
}

export interface Series {
    id: number,
    title: string,
    alternateTitles: AlternateTitle[],
    sortTitle: string,
    status: string,
    ended: boolean,
    profileName: string,
    overview: string,
    nextAiring?: string,
    previousAiring?: string,
    network: string,
    airTime: string,
    images: Image[],
    originalLanguage: Language,
    remotePoster: string,
    seasons: Season[],
    year: number,
    path: string,
    qualityProfileId: number,
    seasonFolder: boolean,
    monitored: boolean,
    monitorNewItems: string,
    useSceneNumbering: boolean,
    runtime: number,
    tvdbId: number,
    tvRageId: number,
    tvMazeId: number,
    tmdbId: number,
    firstAired?: string,
    lastAired?: string,
    seriesType: string,
    cleanTitle: string,
    imdbId: string,
    titleSlug: string,
    rootFolderPath: string,
    folder: string,
    certification: string,
    genres: string[],
    tags: number[],
    added: string,
    addOptions: AddOptions,
    ratings: Ratings,
    statistics: Statistics,
    episodesChanged: boolean
}
