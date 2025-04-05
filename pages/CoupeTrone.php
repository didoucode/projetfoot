<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupe du Trône Maroc 2023-2024</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #00703c;  /* Couleur verte du drapeau marocain */
            color: white;
            padding: 20px 0;
            text-align: center;
            border-bottom: 5px solid #c1272d;  /* Rouge du drapeau marocain */
        }
        
        h1, h2, h3 {
            margin-top: 0;
        }
        
        .tournament-info {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-card {
            background-color: #fff;
            border-left: 4px solid #00703c;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .info-card h3 {
            margin-top: 0;
            color: #00703c;
        }
        
        .match-card {
            background-color: #fff;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .match-card .teams {
            flex: 1;
        }
        
        .match-card .score {
            font-weight: bold;
            padding: 0 10px;
            text-align: center;
            min-width: 60px;
        }
        
        .match-card .date {
            color: #666;
            font-size: 0.9em;
            text-align: right;
            min-width: 100px;
        }
        
        .team {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        
        .team img {
            width: 24px;
            height: 24px;
            margin-right: 10px;
        }
        
        .team.winner {
            font-weight: bold;
        }
        
        /* Styles pour l'arbre du tournoi */
        .tournament-bracket {
            display: flex;
            overflow-x: auto;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .round {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            min-width: 200px;
            margin-right: 40px;
        }
        
        .round-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #00703c;
        }
        
        .bracket-match {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            position: relative;
            height: 80px;
        }
        
        .bracket-match::after {
            content: '';
            position: absolute;
            right: -40px;
            top: 50%;
            width: 40px;
            height: 2px;
            background-color: #ddd;
        }
        
        .final .bracket-match::after {
            display: none;
        }
        
        .bracket-team {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }
        
        .bracket-team-name {
            flex: 1;
        }
        
        .bracket-score {
            font-weight: bold;
            width: 24px;
            text-align: center;
        }
        
        .bracket-winner {
            font-weight: bold;
            background-color: rgba(0, 112, 60, 0.1);
            border-radius: 3px;
        }
        
        .bracket-date {
            font-size: 0.8em;
            color: #666;
            text-align: center;
            margin-top: 5px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .round {
                min-width: 150px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Coupe du Trône 2023-2024</h1>
            <p>La plus prestigieuse compétition de football à élimination directe au Maroc</p>
        </div>
    </header>
    
    <div class="container">
        <section class="tournament-info">
            <h2>Informations sur la compétition</h2>
            <div class="info-grid">
                <div class="info-card">
                    <h3>Date de début</h3>
                    <p>10 septembre 2023</p>
                </div>
                <div class="info-card">
                    <h3>Date de fin prévue</h3>
                    <p>1 juin 2024</p>
                </div>
                <div class="info-card">
                    <h3>Format</h3>
                    <p>Compétition à élimination directe</p>
                </div>
                <div class="info-card">
                    <h3>Tenant du titre</h3>
                    <p>AS FAR (2022-2023)</p>
                </div>
            </div>
        </section>
        
        <section>
            <h2>Arbre du tournoi</h2>
            <div class="tournament-bracket">
                <div class="round">
                    <div class="round-title">1/8 de finale</div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Raja Casablanca</div>
                            <div class="bracket-score">2</div>
                        </div>
                        <div class="bracket-team">
                            <div class="bracket-team-name">Difaâ El Jadida</div>
                            <div class="bracket-score">0</div>
                        </div>
                        <div class="bracket-date">15 déc 2023</div>
                    </div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team">
                            <div class="bracket-team-name">Ittihad Tanger</div>
                            <div class="bracket-score">1</div>
                        </div>
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">AS FAR</div>
                            <div class="bracket-score">2</div>
                        </div>
                        <div class="bracket-date">16 déc 2023</div>
                    </div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Wydad Casablanca</div>
                            <div class="bracket-score">1</div>
                        </div>
                        <div class="bracket-team">
                            <div class="bracket-team-name">FUS Rabat</div>
                            <div class="bracket-score">0</div>
                        </div>
                        <div class="bracket-date">17 déc 2023</div>
                    </div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team">
                            <div class="bracket-team-name">Olympique Khouribga</div>
                            <div class="bracket-score">0</div>
                        </div>
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Renaissance Berkane</div>
                            <div class="bracket-score">2</div>
                        </div>
                        <div class="bracket-date">18 déc 2023</div>
                    </div>
                </div>
                
                <div class="round">
                    <div class="round-title">1/4 de finale</div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Raja Casablanca</div>
                            <div class="bracket-score">3</div>
                        </div>
                        <div class="bracket-team">
                            <div class="bracket-team-name">AS FAR</div>
                            <div class="bracket-score">1</div>
                        </div>
                        <div class="bracket-date">15 fév 2024</div>
                    </div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team">
                            <div class="bracket-team-name">Wydad Casablanca</div>
                            <div class="bracket-score">0</div>
                        </div>
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Renaissance Berkane</div>
                            <div class="bracket-score">1</div>
                        </div>
                        <div class="bracket-date">16 fév 2024</div>
                    </div>
                </div>
                
                <div class="round">
                    <div class="round-title">Demi-finales</div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team bracket-winner">
                            <div class="bracket-team-name">Raja Casablanca</div>
                            <div class="bracket-score">2</div>
                        </div>
                        <div class="bracket-team">
                            <div class="bracket-team-name">Renaissance Berkane</div>
                            <div class="bracket-score">1</div>
                        </div>
                        <div class="bracket-date">12 avr 2024</div>
                    </div>
                </div>
                
                <div class="round final">
                    <div class="round-title">Finale</div>
                    
                    <div class="bracket-match">
                        <div class="bracket-team">
                            <div class="bracket-team-name">Raja Casablanca</div>
                            <div class="bracket-score">-</div>
                        </div>
                        <div class="bracket-team">
                            <div class="bracket-team-name">À déterminer</div>
                            <div class="bracket-score">-</div>
                        </div>
                        <div class="bracket-date">1 juin 2024</div>
                    </div>
                </div>
            </div>
        </section>
        
        <section>
            <h2>Résultats des derniers matchs</h2>
            
            <div class="match-card">
                <div class="teams">
                    <div class="team winner">
                        <img src="/api/placeholder/24/24" alt="Raja">
                        Raja Casablanca
                    </div>
                    <div class="team">
                        <img src="/api/placeholder/24/24" alt="Berkane">
                        Renaissance Berkane
                    </div>
                </div>
                <div class="score">2 - 1</div>
                <div class="date">12/04/2024</div>
            </div>
            
            <div class="match-card">
                <div class="teams">
                    <div class="team">
                        <img src="/api/placeholder/24/24" alt="Wydad">
                        Wydad Casablanca
                    </div>
                    <div class="team winner">
                        <img src="/api/placeholder/24/24" alt="Berkane">
                        Renaissance Berkane
                    </div>
                </div>
                <div class="score">0 - 1</div>
                <div class="date">16/02/2024</div>
            </div>
            
            <div class="match-card">
                <div class="teams">
                    <div class="team winner">
                        <img src="/api/placeholder/24/24" alt="Raja">
                        Raja Casablanca
                    </div>
                    <div class="team">
                        <img src="/api/placeholder/24/24" alt="FAR">
                        AS FAR
                    </div>
                </div>
                <div class="score">3 - 1</div>
                <div class="date">15/02/2024</div>
            </div>
        </section>
    </div>
    
    <footer style="background-color: #333; color: white; padding: 20px 0; text-align: center; margin-top: 40px;">
        <div class="container">
            <p>© 2024 Coupe du Trône - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>