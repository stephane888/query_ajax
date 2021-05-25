[
  // ajout des données.
  {
    table:"nom_de_la_table",
    'fields': {
      'nom_column1': 'valaue',
      'nom_column2': 'valaue',
      'nom_column-n': 'valaue',
    }
  },
  // ajout des données.
  {
    table:"nom_de_la_table-2",
    'fields': {
      'nom_column1': 'valaue',
      'nom_column2': 'valaue',
      'nom_column-n': 'valaue',
    }
  },
  // Mise à jour des données.
  {
    table:"nom_de_la_table-2",
    'fields': {
      'nom_column1': 'valaue',
      'nom_column2': 'valaue',
      'nom_column-n': 'valaue',
    },
    'action':'update',
    'where':[
      {
        'column':'nom de la colonne',
        'value':'valeur à modifier'
      },
      {
        'column':'nom de la colonne',
        'value':'valeur à modifier'
      },
      ...
    ]
  },
  // Ajout des données dans une table et les sous tables.
  // en cas d'erreur toutes les données ajoutés sont annulés;
  {
    table:"nom_de_la_table_parent",
    'fields': {
      'nom_column1': 'valaue',
      'nom_column2': 'valaue',
      'nom_column-n': 'valaue',
    },
    childstable: {
    	colum_id_name:"nom_de_la_colonne",
    	// l'id de la ligne parent va etre automatiquement ajouté aux insertions enfant.
    	tables:[
    		{
    		    table:"nom_de_la_table enfant 1",
    		    'fields': {
    		      'nom_column1': 'valaue',
    		      'nom_column2': 'valaue',
    		      'nom_column-n': 'valaue',
    		    }
    		},
    		{
    		    table:"nom_de_la_table enfant 2",
    		    'fields': {
    		      'nom_column1': 'valaue',
    		      'nom_column2': 'valaue',
    		      'nom_column-n': 'valaue',
    		    }
    		},
    		...
    	]
    },
  },
  {
	// l'insertion renvoit $value_id_row
    table:"gestion_project_contents",
    'fields': {
      'titre': 'valaue',
      'text': 'valaue',
      'type': 'project',
    },
    
    childstable: {
    	colum_id_name:"idcontents",
    	// l'id de la ligne parent va etre automatiquement ajouté aux insertions enfant.
    	tables: [
    		{
    		    table:"gestion_project_times",
    		    'fields': {
    		      'date_depart_proposer': 'valaue',
    		      'date_fin_proposer': 'valaue',
    		      //champs ajoute de maniere automatique.
    		      'idcontents': $value_id_row
    		    }
    		},
    		{
    		    table:"gestion_project_hierachie",
    		    'fields': {
    		      'idcontentsparent': 15,
    		    //champs ajoute de maniere automatique.
    		      'idcontents': $value_id_row
    		    }
    		},
    		...
    	]
    },
  },
  // Supprimer les données.
  {
    table:"nom_de_la_table",
    'fields': {},
    'action':'delete',
    'where':[
        {
          'column':'nom de la colonne',
          'value':'valeur à modifier'
        },
        {
          'column':'nom de la colonne',
          'value':'valeur à modifier'
        },
        ...
      ]
  },
  // Suppression multiple.
  {
    table:"nom_de_la_table",
    'fields': {},
    'action':'delete',
    'where':[
        {
          'column':'nom de la colonne',
          'value':'valeur à modifier'
        },
    ],
    childstable: {
    	tables: [
    		{
    		    table:"gestion_project_times",
    		    'fields': {},
    		    'action':'delete',
    		    'where': [
    		        {
    		          'column':'nom de la colonne',
    		          'value':'valeur à modifier'
    		        }
    		    ]
    		},
    		{
    		    table:"gestion_project_hierachie",
    		    'fields': {},
    		    'action': 'delete',
    		    'where': [
    		        {
    		          'column':'nom de la colonne',
    		          'value':'valeur à modifier'
    		        }
    		    ]
    		},    		
    	]
  	}   
  },
]